<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){

}

function execute_new(){
  $delivery_id = get_request_param('delivery_id');
  $item_id = get_request_param('item_id');
  $return=array();
  if(intval($delivery_id)){
    require_once('deliveries.class.php');
    $return['delivery'] = delivery_get($delivery_id);
  }
  $return['item_id'] = intval($item_id);
  return $return;
}


function execute_create_ajax(){
  $delivery_id = get_request_param('delivery_id');
  $item_id = get_request_param('item_id');
  $value = get_request_param('value');
  require_once('deliveries.class.php');
  $delivery = delivery_get($delivery_id);
  require_once('products.class.php');
  $product_id = Products::create($delivery->supplier->id);
  $product = product_get($product_id);
  $product->update(array('name' => $value));
  echo json_encode(array('location_href' => '/delivery/product_select?delivery_id='.$delivery->id.'&item_id='.intval($item_id).'&product_id='.$product_id));
  exit;
}