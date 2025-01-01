<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('pickups');

require_once('pickups.class.php');

function execute_index(){
  global $user;
  $pickup_id = get_request_param('pickup_id');
  $modus = get_request_param('modus');
  if($modus == ''){
    $modus = 'p';
  }
  $pickup = pickup_get($pickup_id, $user['member_id']);

  $pickup_items = array();
  $order_item_ids = array();
  $product_ids = array();
  require_once('pickup_items.class.php');
  if($modus == 'p'){
    require_once('pickup_items.class.php');
    $puis = new PickupItems(array('pickup_id' => $pickup_id));
    $product_ids = $puis->get_product_ids();
    foreach($puis as $pui){
      $pickup_items[$pui->product_id] = $pui;
      $order_item_ids[] = $pui->order_item_id;
    }
  }elseif($modus == 'd'){
    require_once('inventory.inc.php');
    update_inventory();
    require_once('inventories.class.php');
    $is = new Inventories();
    $product_ids = array();
    foreach($is as $i){
      if(!$i->amount_pieces && !$i->amount_weight){
        continue;
      }
      $product_ids[$i->product_id] = $i->product_id;
    }
    if(empty($product_ids)){
      $product_ids[0] = 0;
    }
    $puis = new PickupItems(array('pickup_id' => $pickup_id, 'product_id' => $product_ids));
    foreach($puis as $pui){
      $pickup_items[$pui->product_id] = $pui;
    }
  }

  if(empty($product_ids)){
    $product_ids[0] = 0;
  }

  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids),array('FIELD(id,'.implode(',',$product_ids).')' => 'ASC'));

  $ps = array();
  foreach($products as $product){
    if($product->type == 'p'){
      $ps[$product->id] = $product;
    }else{ //k or w
      $ps = array($product->id => $product) + $ps;
    }
  }
  $products = $ps;
  $ps = array();
  foreach($products as $product){
    if($product->type == 'k'){
      $ps = array($product->id => $product) + $ps;
    }else{ //p or w
      $ps[$product->id] = $product;
    }
  }
  $products = $ps;

  if(empty($order_item_ids)){
    $order_item_ids[0] = 0;
  }
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('id' => $order_item_ids));

  require_once('members.class.php');
  $suppliers = new Members(array('producer>=' => 1));

  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $product_ids));

  require_once('sql.class.php');
  $brands = SQL::selectKey2Val("SELECT id, name FROM msl_brands", 'id', 'name');
  /*
  $others = array();
  $last_pickup = array();
  if($product_type == 'v'){
    $others = get_info_others();
  }else{
    $pickup_history = get_pickup_history($pickup);
  }
  */
  return array(
    'modus' => $modus,
    'pickup' => $pickup,
    'pickup_items' => $pickup_items,
    'order_items' => $order_items,
    'products' => $products,
    'suppliers' => $suppliers,
    'brands' => $brands,
    'prices' => $prices,
    //'product_type' => $product_type,
    //'others' => $others,
    //'pickup_history' => $pickup_history
  );
}

function execute_new(){
  global $user;
  $pickup = Pickup::create($user['member_id'], $user['user_id']);
  update_pickup_items($pickup->id);
  forward_to_page('/pickup', 'pickup_id='.$pickup->id);
}

function execute_filter_ajax(){
  $pickup_id = get_request_param('pickup_id');
  $field = get_request_param('field');
  $value = get_request_param('value');
  set_request_param($field, $value);
  $return = execute_index();
  $return['template'] = 'index.php';
  $return['layout'] = 'layout_null.php';
  return $return;
}

function pickup_get($pickup_id, $member_id){
  $pickups = new Pickups(array('id' => $pickup_id, 'member_id' => $member_id));
  if(count($pickups)){
    return $pickups->first();
  }
  return null;
}

function update_pickup_items($pickup_id){
  global $user;
  $pickup = pickup_get($pickup_id, $user['member_id']);
  $pickup_date = '2025-01-10';
  if($pickup_date > date('Y-m-d')){
    return;
  }

  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date' => $pickup_date, 'member_id' => $user['member_id']));
  if(!count($orders)){
    return;
  }
  $order = $orders->first();
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $order->id));

  require_once('products.class.php');
  $products = new Products(array('id' => $order_items->get_product_ids()));

  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $order_items->get_product_ids(), 'start<=' => $order->pickup_date, 'end>=' => $order->pickup_date));

  require_once('pickup_items.class.php');
  $puis = new PickupItems(array('pickup_id' => $pickup_id));
  $pickup_items = array();
  foreach($puis as $pui){
    $pickup_items[$pui->product_id] = $pui;
  }

  foreach($order_items as $oi){
    if(!$oi->amount_pieces && !$oi->amount_weight){
      continue;
    }
    if(!isset($pickup_items[$oi->product_id])){
      $pui = PickupItem::create($pickup_id, $oi->product_id);
      $updates = array(
        'order_item_id' => $oi->id,
        'amount_pieces_min' => $oi->amount_pieces,
        'amount_pieces_max' => $oi->amount_pieces,
        'amount_weight_min' => $oi->amount_weight * 0.9,
        'amount_weight_max' => $oi->amount_weight * 1.1,
        'price' => $prices[$oi->product_id]->price,
        'amount_per_bundle' => $prices[$oi->product_id]->amount_per_bundle,
        'price_bundle' => $prices[$oi->product_id]->price_bundle,
      );
      if($products[$oi->product_id]->type == 'p'){
        $updates['price_type'] = 'p';
      }else{ //k or w
        $updates['price_type'] = 'k';
      }
      $pui->update($updates);
      $puis = new PickupItems(array('id' => $pui->id));
      $pui = $puis->first();
      $pickup_items[$oi->product_id] = $pui;
    }
  }

  /*
  $has_type_b = false;
  $type_b_factor = 0;
  $type_b_producer_ids = array();
  require_once('sql.class.php');
  $qry = "SELECT o.pid, p.type, p.producer_id, o.amount, pr.price, pr.purchase, ".
    "SUM(i.amount_pieces) AS i_amount_pieces, SUM(i.amount_weight) AS i_amount_weight ".
    "FROM msl_orders o, msl_prices pr, msl_products p ".
      "LEFT JOIN msl_inventory i ON (p.pid=i.product_id AND i.pickup_item_id IN (0 $pickup_item_ids)) ".
      "LEFT JOIN msl_delivery_items di ON (di.id = i.delivery_item_id) ".
    "WHERE o.pid=p.pid AND o.amount>0 AND o.member_id='".intval($user['member_id'])."' ".
    "AND p.pid=pr.pid AND pr.start<=CURDATE() AND pr.end>=CURDATE() ".
    "GROUP BY o.pid, p.type, p.producer_id, o.amount, pr.price, pr.purchase";
  $orders = SQL::select($qry);
  #logger(print_r($orders,1));
  foreach($orders as $o){
    if($o['pid'] == 59){ //sponsoring -> ignore
      continue;
    }elseif($o['type'] == 'b'){
      $has_type_b = 1;
      $type_b_factor = $o['price'] / $o['purchase'];
      continue;
    }
    if(!isset($pickup_items[$o['pid']])){
      $item = $pickup->item_create($o['pid'], $o['delivery_item_id']);
      $item->update(array(
        'price_type' => $o['type'],
        'price' => $o['price'],
      ));
    }
  }

  if($has_type_b){
    $qry = "SELECT i.product_id, p.producer_id, i.amount_weight, i.amount_pieces, ".
        "i.delivery_item_id, di.price_type di_price_type, di.purchase AS di_purchase ".
      "FROM msl_products p, msl_inventory i ".
      " LEFT JOIN msl_delivery_items di ON (di.id = i.delivery_item_id) ".
      "WHERE i.product_id=p.pid  AND i.pickup_item_id IN (0 $pickup_item_ids) AND p.type='v' ".
      "ORDER BY p.name, i.delivery_item_id DESC";
    $vproducts = SQL::select($qry);
    #logger("vproducts ".print_r($vproducts,1));
    foreach($vproducts as $p){
      if(!isset($pickup_items[$p['product_id']])){
        $item = $pickup->item_create($p['product_id'], $p['delivery_item_id']);
        $item->update(array(
          'price_type' => $p['di_price_type'],
          'price' => round($p['di_purchase'] * $type_b_factor, 2),
        ));
        $pickup_items[$p['product_id']] = $item;
        $pickup_item_ids .= ','.$item->id;
      }
    }
  }
  */
}

function execute_change_ajax(){
  global $user;
  $pickup_id = get_request_param('pickup_id');
  $product_id = intval(get_request_param('product_id'));
  $change = get_request_param('change');
  $modus = get_request_param('modus');
  logger("$pickup_id $product_id $change $modus");
  if($pickup_id && $product_id && $change){
    require_once('pickup_items.class.php');
    $puis = new PickupItems(array('pickup_id' => $pickup_id, 'product_id' => $product_id));
    if(!$puis->count()){
      $pui = PickupItems::create($pickup_id, $product_id);
    }else{
      $pui = $puis->first();
    }
    require_once('products.class.php');
    $ps = new Products(array('id' => $product_id));
    $product = $ps->first();
    if($product->type == 'p' || $product->type == 'w'){
      $amount = $pui->amount_pieces;
      $amount_field = 'amount_pieces';
    }elseif($product->type == 'k'){
      $amount = $pui->amount_weight;
      $amount_field = 'amount_weight';
    }else{
      throw new Exception("unknown product-type ".print_r($product,1), 1);
      exit;
    }

    if($change == '+' && $product->status == 'o'){
      $amount_new = round($amount + $product->amount_steps, 3);
    }elseif($change == '+' && $product->status == 's'){
      $amount_new = round($amount + $product->amount_per_bundle, 3);
    }elseif($change == '-' && $product->status == 'o'){
      $amount_new = round($amount - $product->amount_steps, 3);
    }elseif($change == '-' && $product->status == 's'){
      $amount_new = round($amount - $product->amount_per_bundle, 3);
    }elseif($change == '='){
      require_once('order_items.class.php');
      $ois = new OrderItems(array('id' => $pui->order_item_id));
      $oi = $ois->first();
      $amount_new = $oi->amount_pieces;
    }

    if($amount_new < $product->amount_min && $change == '-'){
      $amount_new = 0;
    }elseif($amount_new < $product->amount_min && $change == '+'){
      $amount_new = $product->amount_min;
    }elseif($amount_new > $product->amount_max){
      $amount_new = $product->amount_max;
    }
    logger("amount_field $amount_field amount_new $amount_new");
    $pui->update(array($amount_field => $amount_new));
    update_pickup_item_price_sum($pui->id);
  }
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}

function execute_scale_ajax(){
  global $user;
  $pickup_id = intval(get_request_param('pickup_id'));
  $pickup_item_id = intval(get_request_param('item_id'));
  $value = get_request_param('value');
  $pu = Pickups::sget($pickup_id);
  if(!$pu || $pu->member_id!=$user['member_id']){
    logger("ERROR wrong pickup $pickup_id ".$user['member_id']);
    exit;
  }
  require_once('pickup_items.class.php');
  $pui = PickupItems::sget($pickup_item_id);
  if(!$pui || $pui->pickup_id!=$pickup_id){
    logger("ERROR wrong pickup item $pickup_item_id");
    exit;
  }
  $pui->update(array('amount_weight' => floatval($value)));
  update_pickup_item_price_sum($pui->id);
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}

function update_pickup_item_price_sum($pickup_item_id){
  require_once('pickup_items.class.php');
  $pui = PickupItems::sget($pickup_item_id);
  $amount = 0;
  if($pui->price_type == 'k'){
    $amount = $pui->amount_weight;
  }elseif($pui->price_type == 'p'){
    $amount = $pui->amount_pieces;
  }else{
    logger("ERROR wrong price_type ".print_r($pui,1));
    return;
  }
  $price = $pui->price;
  logger("update_pickup_item_price_sum $amount ".print_r($pui,1));
  if(($pui->amount_per_bundle > 0) && ($pui->price_bundle > 0) && ($amount >= $pui->amount_per_bundle)){
    $price = $pui->price_bundle;
  }
  $price_sum = round($amount * $price, 2);
  if($pui->price_sum != $price_sum){
    $pui->update(array('price_sum' => $price_sum));
  }
}


/*

function execute_update_ajax(){
  global $user;
  $pickup_id = get_request_param('pickup_id');
  $item_id = get_request_param('item_id');
  $value = get_request_param('value');
  $pickup = pickup_get($pickup_id, $user['member_id']);
  $item = $pickup->items[$item_id];
  $field = '';
  #logger("item ".print_r($item, true));
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
*/

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
  $deliveries = new Deliveries(array('supplier_id' => array(11 /*Sigi Klein*/)), array('d.id' => 'DESC'), 0, 1);
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
    if($order->product->type == 'b' && $order->amount > 0 && $order->product->id != 59){
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
  return array();
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