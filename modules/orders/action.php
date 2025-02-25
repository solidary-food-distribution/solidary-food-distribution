<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  require_once('orders.class.php');
  $orders = new Orders(array('member_id' => $user['member_id']),array('pickup_date'=>'DESC'), 0, 10);
  require_once('delivery_dates.class.php');
  $delivery_dates = new DeliveryDates(array('date>=' => date('Y-m-d')), array('date' => 'ASC'), 0, 3);
  $pickup_dates = array();
  foreach($delivery_dates as $delivery_date){
    $pickup_dates[$delivery_date->date] = 0;
  }
  foreach($orders as $order){
    $pickup_dates[$order->pickup_date] = 1;
  }
  foreach($pickup_dates as $pickup_date => $exists){
    if(!$exists){
      Order::create($user['member_id'], $pickup_date);
    }
  }
  $from = date('Y-m-d', strtotime('-7 days', time()));
  $os = new Orders(array('member_id' => $user['member_id'], 'pickup_date>=' => $from),array('pickup_date'=>'DESC'),0,4);
  $orders = array();
  foreach($os as $o){
    $orders = array($o->id => $o) + $orders;
  }
  return array('orders' => $orders);
}
