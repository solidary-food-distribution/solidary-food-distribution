<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('pickups');

require_once('pickups.class.php');

function execute_index(){
  global $user;
  $pickup_id = get_request_param('pickup_id');
  $product_type = get_request_param('product_type');
  update_pickup_items($pickup_id);
  $pickup = pickup_get($pickup_id, $user['member_id']);
  require_once('orders.class.php');
  $orders = new Orders(array('member_id' => $user['member_id']));
  $others = array();
  $last_pickup = array();
  if($product_type == 'v'){
    $others = get_info_others();
  }else{
    $pickup_history = get_pickup_history($pickup);
  }
  return array(
    'pickup' => $pickup,
    'product_type' => $product_type,
    'orders' => $orders,
    'others' => $others,
    'pickup_history' => $pickup_history);
}

function execute_new(){
  global $user;
  $pickup_id = Pickups::create($user['member_id'], $user['user_id']);
  update_pickup_items($pickup_id);
  forward_to_page('/pickup', 'pickup_id='.$pickup_id);
}


function update_pickup_items($pickup_id){
  global $user;
  $pickup = pickup_get($pickup_id, $user['member_id']);
  if($pickup->status != 'o'){
    return;
  }
  $pickup_items = array();
  $pickup_item_ids = '';
  foreach($pickup->items as $item){
    $pickup_items[$item->product->id] = $item;
    $pickup_item_ids .= ','.$item->id;
  }
  $type_b_producer_ids = array();
  require_once('sql.class.php');
  $qry = "SELECT o.pid, p.type, p.producer_id, o.amount, ".
    "SUM(i.amount_pieces) AS i_amount_pieces, SUM(i.amount_weight) AS i_amount_weight, ".
    "i.delivery_item_id, MAX(di.price_type) AS di_price_type, MAX(di.price) AS di_price ".
    "FROM msl_orders o, msl_products p ".
      "LEFT JOIN msl_inventory i ON (p.pid=i.product_id AND i.pickup_item_id IN (0 $pickup_item_ids)) ".
      "LEFT JOIN msl_delivery_items di ON (di.id = i.delivery_item_id) ".
    "WHERE o.pid=p.pid AND o.amount>0 AND o.member_id='".intval($user['member_id'])."' ".
    "GROUP BY o.pid, p.type, p.producer_id, o.amount";
  $orders = SQL::select($qry);
  #logger(print_r($orders,1));
  foreach($orders as $o){
    if($o['type'] == 'b'){
      $type_b_producer_ids[ $o['producer_id'] ] = 1;
      continue;
    }
    if(!isset($pickup_items[$o['pid']])){
      $item = $pickup->item_create($o['pid'], $o['delivery_item_id']);
      $item->update(array(
        'price_type' => $o['type'],
        'price' => $o['di_price'],
      ));
    }
  }

  #logger(print_r($type_b_producer_ids,true));
  if(empty($type_b_producer_ids)){
    $type_b_producer_ids=array(0);
  }

  $qry = "SELECT i.product_id, p.producer_id, i.amount_weight, i.amount_pieces, ".
      "i.delivery_item_id, di.price_type di_price_type, di.price AS di_price ".
    "FROM msl_products p, msl_inventory i ".
    " LEFT JOIN msl_delivery_items di ON (di.id = i.delivery_item_id) ".
    "WHERE i.product_id=p.pid  AND i.pickup_item_id IN (0 $pickup_item_ids) AND p.type='v' AND p.producer_id IN (".SQL::escapeArray(array_keys($type_b_producer_ids)).") ".
    "ORDER BY p.name, i.delivery_item_id DESC";
  $vproducts = SQL::select($qry);
  foreach($vproducts as $p){
    if(!isset($pickup_items[$p['product_id']])){
      $item = $pickup->item_create($p['product_id'], $p['delivery_item_id']);
      $item->update(array(
        'price_type' => $p['di_price_type'],
        'price' => $p['di_price'],
      ));
      $pickup_items[$p['product_id']] = $item;
      $pickup_item_ids .= ','.$item->id;
    }
  }
}

function execute_update_ajax(){
  global $user;
  $pickup_id = get_request_param('pickup_id');
  $item_id = get_request_param('item_id');
  $value = get_request_param('value');
  $pickup = pickup_get($pickup_id, $user['member_id']);
  $item = $pickup->items[$item_id];
  $field = '';
  logger("item ".print_r($item, true));
  if($item->price_type == 'k'){
    $field = 'amount_weight';
    $value = str_replace(',', '.', $value);
    $value = number_format(floatval($value), 3, '.', '');
  }elseif($item->price_type == 'p'){
    $field = 'amount_pieces';
    if($value == '+'){
      $value = $item->amount_pieces + 1;
    }elseif($value == '-'){
      $value = $item->amount_pieces - 1;
      if($value < 0){
        $value = 0;
      }
    }
    $value = str_replace(',', '.', $value);
    $value = number_format(floatval($value), 2, '.', '');
  }
  if($field){
    $updates = array(
      $field => $value,
      'price_sum' => round($value * $item->price, 2)
    );
    $item->update($updates);
    inventory_update($item->id, $item->product_id, array($field => $value));
  }
  $return = execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}

function inventory_update($pickup_item_id, $product_id, $updates){
  require_once('inventories.class.php');
  $objects = new Inventories(array('pickup_item_id' => $pickup_item_id));
  if(count($objects)){
    $inventory = $objects->first();
  }else{
    $id = Inventories::create(0, $pickup_item_id, $product_id);
    $inventory = inventory_get($id);
  }
  foreach($updates as $field=>$value){
    if(!isset($inventory->{$field})){
      unset($updates[$field]);
    }
  }
  if(!empty($updates)){
    if(isset($updates['amount_pieces'])){
      $updates['amount_pieces'] *= -1;
    }
    if(isset($updates['amount_weight'])){
      $updates['amount_weight'] *= -1;
    }
    #logger(print_r($updates,1));
    $inventory->update($updates);
  }
}

function get_info_others(){
  global $user;
  require_once('deliveries.class.php');
  $deliveries = new Deliveries(array('supplier_id' => 11 /*Sigi Klein*/), array('d.id' => 'DESC'), 0, 1);
  $delivery = $deliveries->first();
  $last = $delivery->created->format('Y-m-d H:i');
  $pickups = new Pickups(array('created>' => $last, 'm.id!=' => $user['member_id']));
  $pickup_sum = 0;
  $pickup_count = array();
  #logger(print_r($pickups,1));
  foreach($pickups as $pickup){
    foreach($pickup->items as $item){
      if($item->product->type == 'v' && $item->price_sum > 0){
        $pickup_sum += $item->price_sum;
        $pickup_count[$pickup->member->id]=1;
      }
    }
  }
  require_once('orders.class.php');
  $orders = new Orders(array('o.member_id!=' => $user['member_id'], 'p.type' => 'b'));
  $orders_sum = 0;
  $orders_count = 0;
  $open_sum = 0;
  foreach($orders as $order){
    if($order->product->type == 'b' && $order->amount > 0){
      $orders_sum += $order->amount;
      if(!isset($pickup_count[$order->member->id])){
        $open_sum += $order->amount;
      }
      $orders_count++;
    }
  }
  return array(
    'pickup_sum' => $pickup_sum,
    'pickup_count' => count($pickup_count),
    'orders_sum' => $orders_sum,
    'orders_count' => $orders_count,
    'open_sum' => $open_sum);
}

function get_pickup_history($pickup){
  global $user;
  $pickup_history = array();
  foreach($pickup->items as $item){
    if($item->product->type != 'v'){
      $pickup_history[$item->product->id] = array();
    }
  }

  $pickups = new Pickups(array('member_id' => $user['member_id'], 'pu.id!=' => $pickup->id, 'pui.product_id' => array_keys($pickup_history)), array('pu.id' => 'DESC'));
  foreach($pickups as $p){
    foreach($p->items as $item){
      if($item->product->type != 'v' && ($item->amount_pieces>0 ||$item->amount_weight>0)){
        $date = $p->created->format('d.m.y');
        $pickup_history[$item->product->id][$date] = ($item->amount_pieces?$item->amount_pieces:$item->amount_weight);
      }
    }
  }
  return $pickup_history;
}