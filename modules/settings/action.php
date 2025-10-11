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
  $members = new Members(array('pate_id' => $user['member_id'], 'status' => array('a', 'c', 'i')));
  return array('members' => $members);
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