<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('pickups');

function execute_index(){
  global $user;
  require_once('pickups.class.php');
  $pickups=new Pickups(array('member_id' => $user['member_id']),array(),-20);
  return array('pickups'=>$pickups);
}