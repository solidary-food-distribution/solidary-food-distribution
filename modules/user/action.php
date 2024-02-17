<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  return array('user' => $user, 'pickup_pin' => get_pickup_pin());
}

function execute_pickup_pin(){
  global $user;
  return array('user' => $user, 'pickup_pin' => get_pickup_pin());
}

function get_pickup_pin(){
  global $user;
  require_once('sql.class.php');
  $qry = "SELECT pickup_pin FROM msl_users WHERE id='".SQL::escapeString($user['user_id'])."'";
  $pickup_pin = SQL::selectOne($qry)['pickup_pin'];
  return $pickup_pin;
}

function execute_pickup_pin_ajax(){
  global $user;
  $pickup_pin = get_request_param('pickup_pin');
  $pickup_pin = explode(',', $pickup_pin);
  if(count($pickup_pin)<3 || count($pickup_pin)>6){
    exit;
  }
  $pin = '';
  foreach($pickup_pin as $id){
    $id = intval($id);
    if($id<=0 || $id>32){
      exit;
    }
    $pin .= str_pad($id, 2, '0', STR_PAD_LEFT);
  }
  require_once('sql.class.php');
  $error = '';
  $qry = "SELECT COUNT(*) cnt FROM msl_users WHERE id!='".SQL::escapeString($user['user_id'])."' AND pickup_pin='".SQL::escapeString($pin)."'";
  $count = SQL::selectOne($qry)['cnt'];
  if($count){
    $error = 'Diese PIN kann nicht vergeben werden.';
  }else{
    SQL::update("UPDATE msl_users SET pickup_pin='".SQL::escapeString($pin)."' WHERE id='".SQL::escapeString($user['user_id'])."'");
  }
  echo json_encode(array('error' => $error));
  exit;
}