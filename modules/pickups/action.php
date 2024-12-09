<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('pickups');

function execute_index(){
  global $user;
  require_once('pickups.class.php');
  $pickups = new Pickups(array('member_id' => $user['member_id']),array(),-3);
  $pickup_ids = $pickups->keys();
  if(empty($pickup_ids)){
    $pickup_ids = array(0 => 0);
  }
  require_once('pickup_items.class.php');
  $puis = new PickupItems(array('pickup_id' => $pickup_ids));
  $pickup_items = array();
  foreach($puis as $pui_id => $pui){
    $pickup_items[$pui->pickup_id][$pui_id] = $pui;
  }
  $product_ids = $puis->get_product_ids();
  if(empty($product_ids)){
    $product_ids = array(0 => 0);
  }
  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));
  return array('pickups'=>$pickups, 'pickup_items' => $pickup_items, 'products' => $products);
}


function execute_delete_ajax(){
  global $user;
  $pickup_id = get_request_param('pickup_id');
  require_once('pickups.class.php');
  $pickups = new Pickups(array('id' => $pickup_id, 'member_id' => $user['member_id']),array(),-5);
  logger(print_r($pickups,1));
  //TODO check if all items amount=0
  if($pickups->isset($pickup_id)){
    logger("delete");
    $pickups[$pickup_id]->delete();
  }
  exit;
}
