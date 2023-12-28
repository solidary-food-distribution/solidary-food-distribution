<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('pickups');

require_once('pickups.class.php');

function execute_index(){
  $pickup_id = get_request_param('pickup_id');
  $item_id = get_request_param('item_id');
  update_pickup_items($pickup_id);
  $pickup = pickup_get($pickup_id);
  return array('pickup' => $pickup, 'item_id' => $item_id);
}

function execute_new(){
  global $user;
  $pickup_id = Pickups::create($user['member_id'], $user['user_id']);
  update_pickup_items($pickup_id);
  forward_to_page('/pickup', 'pickup_id='.$pickup_id);
}


function update_pickup_items($pickup_id){
  global $user;
  $pickup = pickup_get($pickup_id);
  if($pickup->status != 'o'){
    return;
  }
  $pickup_items = array();
  foreach($pickup->items as $item){
    $pickup_items[$item->product->id] = $item;
  }
  $type_b_producer_ids = array();
  require_once('sql.class.php');
  $qry = "SELECT o.pid, p.type, p.producer_id, o.amount, ".
    "SUM(i.amount_pieces) AS i_amount_pieces, SUM(i.amount_weight) AS i_amount_weight ".
    "FROM msl_orders o, msl_products p LEFT JOIN msl_inventory i ON (p.pid=i.product_id) ".
    "WHERE o.pid=p.pid AND o.amount>0 AND o.member_id='".intval($user['member_id'])."' ".
    "GROUP BY o.pid, p.type, p.producer_id, o.amount";
  $orders = SQL::select($qry);
  logger(print_r($orders,1));
  foreach($orders as $o){
    if($o['type'] == 'b'){
      $type_b_producer_ids[ $o['producer_id'] ] = 1;
      continue;
    }
    if(!isset($pickup_items[$o['pid']])){
      $item = $pickup->item_create($o['pid']);
    }
  }

  logger(print_r($type_b_producer_ids,true));

  $qry = "SELECT i.product_id, p.producer_id, i.amount_weight, i.amount_pieces ".
    "FROM msl_inventory i, msl_products p ".
    "WHERE i.product_id=p.pid AND p.type='v' AND p.producer_id IN (".SQL::escapeArray(array_keys($type_b_producer_ids)).") ".
    "ORDER BY p.name";
  $vproducts = SQL::select($qry);
  foreach($vproducts as $p){
    if(!isset($pickup_items[$p['product_id']])){
      $item = $pickup->item_create($p['product_id']);
    }
  }
}
