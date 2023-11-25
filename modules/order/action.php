<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  require_once('orders.class.php');
  $orders = new Orders(array('member_id' => $user['member_id']));
  return array('orders' => $orders);
}

function execute_change_ajax(){
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
}