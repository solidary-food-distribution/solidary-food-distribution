<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('pickups');

function execute_index(){
  global $user;
  require_once('pickups.class.php');
  $pickups = new Pickups(array('member_id' => $user['member_id']),array('id' => 'DESC'), 0, 3);
  $pickup_ids = $pickups->keys();
  $pickups_array = array_reverse($pickups->array(), true);

  require_once('pickup_items.class.php');
  $puis = new PickupItems(array('pickup_id' => $pickup_ids));
  $pickup_items = array();
  $order_item_ids = array();
  foreach($puis as $pui_id => $pui){
    $pickup_items[$pui->pickup_id][$pui_id] = $pui;
    $order_item_ids[] = $pui->order_item_id;
  }

  may_auto_create_pickup($order_item_ids);

  $product_ids = $puis->get_product_ids();
  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));
  return array('pickups'=>$pickups_array, 'pickup_items' => $pickup_items, 'products' => $products);
}

function may_auto_create_pickup($order_item_ids){
  global $user;
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('id' => $order_item_ids));
  $order_ids = array();
  foreach($order_items as $order_item){
    $order_ids[$order_item->order_id] = $order_item->order_id;
  }
  #logger("may_auto_create_pickup order_ids ".print_r($order_ids,1));
  require_once('orders.class.php');
  $orders = new Orders(array('member_id' => $user['member_id'], 'pickup_date<=' => date('Y-m-d')), array('pickup_date' => 'DESC'), 0, 10);
  #logger("may_auto_create_pickup orders ".print_r($orders,1));
  if($orders->count()){
    $order = $orders->first();
    if(!isset($order_ids[$order->id])){
      $pickup = Pickup::create($user['member_id'], $user['user_id']);
      require_once('pickups.inc.php');
      update_pickup_items($pickup->id);
      forward_to_page('/pickup', 'pickup_id='.$pickup->id);
    }
  }
}

function execute_delete_ajax(){
  global $user;
  $pickup_id = get_request_param('pickup_id');
  require_once('pickups.class.php');
  $pickups = new Pickups(array('id' => $pickup_id, 'member_id' => $user['member_id']),array('id' => 'DESC'), 0, 3);
  logger(print_r($pickups,1));
  //TODO check if all items amount=0
  if($pickups->isset($pickup_id)){
    logger("delete");
    $pickups[$pickup_id]->delete();
  }
  exit;
}
