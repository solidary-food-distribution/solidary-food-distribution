<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  require_once('order.class.php');
  $order=new Order($user['member_id']);
  return array('order'=>$order);
}

function execute_change_ajax(){
  global $user;
  $error='';
  $product_id=intval(get_request_param('product_id'));
  $amount=floatval(get_request_param('amount'));
  $dir=get_request_param('dir');
  $amount_unit_html='';
  $amount_data='';
  if(!$product_id){
    $error='no product_id';
  }
  if(empty($error)){
    require_once('order_product.class.php');
    $op=new OrderProduct($product_id,$user['member_id']);
    $error=$op->change_amount($dir,$amount);
    if($error===''){
      $amount_formated=$op->get_amount_formated();
      $amount_data=$op->amount;
    }
  }
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}