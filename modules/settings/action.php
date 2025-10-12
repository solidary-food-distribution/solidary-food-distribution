<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  require_once('members.class.php');
  $member = member_get($user['member_id']);
  $membertest = $member->pate_id ? 0 : 1;

  return array('membertest' => $membertest);
}

function execute_membertest(){
  global $user;
  require_once('members.class.php');
  $member = member_get($user['member_id']);
  if($member->pate_id){
    forward_to_noaccess();
  }
  $members = new Members(array('pate_id' => $user['member_id'], 'status' => array('a', 'c', 'i')), array('id' => 'DESC'));
  $member_history = array();
  require_once('orders.class.php');
  $orders = new Orders(array('member_id' => $members->keys()));
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $orders->keys()));
  $order_sums = array();
  foreach($order_items as $order_item){
    $order_sums[$order_item->order_id] += $order_item->price_sum;
  }
  foreach($orders as $order){
    $order = get_object_vars($order);
    $order['price_sum'] = $order_sums[$order['id']];
    if($order['price_sum']){
      $member_history[$order['member_id']][$order['pickup_date']]['order'] = $order;
    }
  }
  require_once('pickups.class.php');
  $pickups = new Pickups(array('member_id' => $members->keys()));
  require_once('pickup_items.class.php');
  $pickup_items = new PickupItems(array('pickup_id' => $pickups->keys()));
  $pickup_sums = array();
  foreach($pickup_items as $pickup_item){
    $pickup_sums[$pickup_item->pickup_id] += $pickup_item->price_sum;
  }
  foreach($pickups as $pickup){
    $pickup_date = substr($pickup->created,0,10);
    $pickup = get_object_vars($pickup);
    $pickup['price_sum'] = $pickup_sums[$pickup['id']];
    if($pickup['price_sum']){
      $member_history[$pickup['member_id']][$pickup_date]['pickup'] = $pickup;
    }
  }
  require_once('debits.class.php');
  $debits = new Debits(array('member_id' => $members->keys()));
  foreach($debits as $debit){
    $pickup_date = substr($pickups[$debit->pickup_id]->created, 0, 10);
    $member_history[$debit->member_id][$pickup_date]['debit' ] = $debit;
  }
  logger(print_r($member_history,1));
  return array('members' => $members, 'member_history' => $member_history);
}

function execute_membertest_new(){
  global $user;
  require_once('members.class.php');
  $member = member_get($user['member_id']);
  if($member->pate_id){
    forward_to_noaccess();
  }
  $member_id = Members::create('.Neue Patenschaft von '.$member->name);
  $new = member_get($member_id);
  $new->update(array('pate_id' => $user['member_id'], 'deactivate_on' => date('Y-m-d', strtotime('+5 weeks', time())) , 'order_limit' => '200'));
  create_membertest_user($member_id);
  send_email('info@mit-sinn-leben.de', 'Neue Patenschaft von '.$member->name.': Member-ID '.$member_id, '');
  forward_to_page('/settings/membertest_edit', 'member_id='.$member_id);
}

function create_membertest_user($member_id){
  require_once('users.class.php');
  $user_id = Users::create('Email setzen '.date('ymdHis'), '.Neue Patenschaft von '.$member->name, $member_id);
  require_once('sql.class.php');
  $qry = "INSERT INTO msl_access (user_id, access, member_id, start, end) VALUES ".
    "($user_id, 'order', $member_id, '0000-00-00', '9999-12-31'),".
    "($user_id, 'pickups', $member_id, '0000-00-00', '9999-12-31')";
  SQL::insert($qry);
}

function execute_membertest_edit(){
  global $user;
  $member_id = get_request_param('member_id');
  require_once('members.class.php');
  $member = member_get($member_id);
  if($member->pate_id != $user['member_id']){
    forward_to_noaccess();
  }
  require_once('users.class.php');
  $musers = new Users(array('member_id' => $member->id));
  $muser = $musers->first();
  return array(
    'member' => $member,
    'muser' => $muser,
  );
}

function execute_membertest_update_ajax(){
  global $user;
  $member_id = get_request_param('member_id');
  require_once('members.class.php');
  $member = member_get($member_id);
  if($member->pate_id != $user['member_id']){
    exit;
  }
  $field = get_request_param('field');
  $value = get_request_param('value');
  logger("$member_id $field $value");
  if($field == 'email' || $field == 'name'){
    require_once('users.class.php');
    $musers = new Users(array('member_id' => $member->id));
    $muser = $musers->first();
    $muser->update(array($field => $value));
  }
  if($field == 'name'){
    $member->update(array($field => $value));
  }elseif($field == 'deactivate_on' && strtotime($value) > 0){
    $value = date('Y-m-d', strtotime($value));
    $member->update(array($field => $value));
  }elseif($field == 'order_limit'){
    $member->update(array($field => intval($value)));
  }

  echo json_encode(array('value' => $value));
  exit;
}