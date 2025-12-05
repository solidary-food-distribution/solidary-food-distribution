<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('pickups');

require_once('pickups.class.php');
require_once('pickups.inc.php');

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
  $inventory = array();
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
    require_once('products.class.php');
    $products = new Products(array('supplier_id' => array(35,61), 'status' => 'o'), array('name' => 'ASC'));
    $product_ids = $products->keys();
    /*
    $product_ids = array_keys($inventory);
    if(empty($product_ids)){
      $product_ids[0] = 0;
    }
    */
    $puis = new PickupItems(array('pickup_id' => $pickup_id, 'product_id' => $product_ids));
    foreach($puis as $pui){
      $pickup_items[$pui->product_id] = $pui;
    }
  }

  require_once('inventory.inc.php');
  $is = get_inventory();
  #logger(print_r($is,1));
  foreach($is as $product_id => $i){
    if(!$i['amount_pieces'] && !$i['amount_weight']){
      continue;
    }
    $inventory[$product_id] = $i;
  }

  if(empty($product_ids)){
    $product_ids[0] = 0;
  }

  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids),array('FIELD(id,'.implode(',',$product_ids).')' => 'ASC'));

  $ps = array();
  foreach($products as $product){
    if($modus == 'd' && $product->supplier_id != 35 && $product->supplier_id != 61){
      continue;
    }
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

  $others = pickups_get_info_others();

  return array(
    'modus' => $modus,
    'pickup' => $pickup,
    'pickup_items' => $pickup_items,
    'order_items' => $order_items,
    'products' => $products,
    'suppliers' => $suppliers,
    'brands' => $brands,
    'prices' => $prices,
    'inventory' => $inventory,
    'others' => $others,
  );
}

function execute_new(){
  global $user;
  $pickup = Pickup::create($user['member_id'], $user['user_id']);
  update_pickup_items($pickup->id);
  $modus = 'p';
  require_once('pickup_items.class.php');
  $pickup_items = new PickupItems(array('pickup_id' => $pickup->id));
  if($pickup_items->count() == 0){
    $modus = 'd';
  }
  forward_to_page('/pickup', 'pickup_id='.$pickup->id.'&modus='.$modus);
}

function execute_update_items(){
  $pickup_id = get_request_param('pickup_id');
  update_pickup_items($pickup_id);
  die("updated.");
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
      $pui = PickupItem::create($pickup_id, $product_id);
    }else{
      $pui = $puis->first();
    }
    require_once('products.class.php');
    $product = Products::sget($product_id);
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
    #logger("amount_field $amount_field amount_new $amount_new");
    $pui->update(array($amount_field => $amount_new));
    update_pickup_item_price_sum($pui->id);
    require_once('inventory.inc.php');
    update_inventory_product($product_id);
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
  $updates = array('amount_weight' => floatval($value));
  if(floatval($value) && $pui->amount_pieces == 0){
    require_once('order_items.class.php');
    $ois = new OrderItems(array('id' => $pui->order_item_id));
    if($ois->count()){
      $oi = $ois->first();
      $updates['amount_pieces'] = $oi->amount_pieces;
    }
  }
  $pui->update($updates);
  update_pickup_item_price_sum($pui->id);
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
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