<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  require_once('orders.class.php');
  $orders = new Orders(array('member_id' => $user['member_id']),array('pickup_date'=>'ASC'),0,3);
  if(!count($orders)){
    Order::create($user['member_id'], '2024-12-06');
    $orders = new Orders(array('member_id' => $user['member_id']),array('pickup_date'=>'ASC'),0,3);
  }
  return array('orders'=>$orders);
}
