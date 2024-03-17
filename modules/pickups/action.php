<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('pickups');

function execute_index(){
  global $user;
  require_once('pickups.class.php');
  $pickups = new Pickups(array('member_id' => $user['member_id']),array(),-5);
  return array('pickups'=>$pickups);
}

function execute_delete_ajax(){
  global $user;
  $pickup_id = get_request_param('pickup_id');
  require_once('pickups.class.php');
  $pickups = new Pickups(array('member_id' => $user['member_id']),array(),-5);
  //TODO check if all items amount=0
  if(isset($pickups[$pickup_id])){
    $pickups[$pickup_id]->delete();
  }
  exit;
}