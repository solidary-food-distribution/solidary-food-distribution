<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  require_once('orders.class.php');
  $orders = new Orders(array('member_id' => $user['member_id']),array('pickup_date'=>'DESC'), 0, 10);
  $pickup_dates = array('2025-01-10' => 0, '2025-01-24' => 0);
  foreach($orders as $order){
    $pickup_dates[$order->pickup_date] = 1;
  }
  foreach($pickup_dates as $pickup_date => $exists){
    if(!$exists){
      Order::create($user['member_id'], $pickup_date);
    }
  }
  $os = new Orders(array('member_id' => $user['member_id'], 'pickup_date>=' => date('Y-m-d')),array('pickup_date'=>'DESC'),0,3);
  $orders = array();
  foreach($os as $o){
    $orders = array($o->id => $o) + $orders;
  }
  return array('orders' => $orders);
}
