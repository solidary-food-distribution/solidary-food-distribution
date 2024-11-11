<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  $id = get_request_param('id');
  require_once('orders.class.php');
  $orders = new Orders(array('member_id' => $user['member_id']),array('pickup_date'=>'DESC'));
  if(!$orders->isset($id)){
    $id = find_actual_order_id($orders);
    if($id){
      forward_to_page('/order?id='.$id);
    }else{
      forward_to_page('/orders');
    }
  }
  $order = $orders->get($id);
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $id));
  $product_ids = $order_items->get_product_ids();
  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));
  return array('order' => $order, 'order_items' => $order_items, 'products' => $products);
}

function find_actual_order_id($orders){
  if(!$orders->count()){
    return 0;
  }
  $prev_id = 0;
  if($orders->first()->pickup_date > date('Y-m-d')){
    $prev_id = $orders->first()->id;
  }
  foreach($orders->array() as $id => $order){
    if($order->pickup_date <= date('Y-m-d')){
      break;
    }
    $prev_id = $id;
  }
  return $prev_id;
}

function execute_change_ajax(){
  /*
  global $user;
  $error='';
  $product_id=intval(get_request_param('product_id'));
  $amount=floatval(get_request_param('amount'));
  $dir=get_request_param('dir');
  if(!$product_id){
    $error='no product_id';
  }
  if(empty($error)){
    require_once('orders.class.php');
    $orders = new Orders(array('product_id' => $product_id, 'member_id' => $user['member_id']));
    $order = $orders->first();
    $error=$order->change_amount($dir,$amount);
  }
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
  */
}