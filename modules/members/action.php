<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('members');

require_once('members.class.php');
require_once('users.class.php');

function execute_index(){
  $members = new Members();
  return array(
    'members' => $members,
    'users' => get_users()
  );
}

function execute_edit(){
  $member_id = get_request_param('member_id');
  $member = member_get($member_id);
  return array(
    'member' => $member,
    'users' => get_users($member_id)
  );
}

function get_users($member_id = 0){
  $users = array();
  $where = array();
  if($member_id){
    $where = array('member_id' => $member_id);
  }
  foreach(new Users($where) as $user){
    if(!isset($users[$user->member_id])){
      $users[$user->member_id] = array();
    }
    $users[$user->member_id][] = $user;
  }
  return $users;
}

function execute_new(){
  $member_id = Members::create('.Neues Mitglied');
  create_user($member_id);
  forward_to_page('/members/edit', 'member_id='.$member_id);
}

function execute_create_user(){
  $member_id = get_request_param('member_id');
  create_user($member_id);
  forward_to_page('/members/edit', 'member_id='.$member_id);
}

function create_user($member_id){
  $user_id = Users::create('Email setzen '.date('ymdHis'), '.Neuer Benutzer', $member_id);
  require_once('sql.class.php');
  $qry = "INSERT INTO msl_access (user_id, access, member_id, start, end) VALUES ".
    "($user_id, 'access', $member_id, '0000-00-00', '9999-12-31'),".
    "($user_id, 'order', $member_id, '0000-00-00', '9999-12-31'),".
    "($user_id, 'pickups', $member_id, '0000-00-00', '9999-12-31'),".
    "($user_id, 'preferences', $member_id, '0000-00-00', '9999-12-31')";
  SQL::insert($qry);
}

function execute_update_ajax(){
  $member_id = get_request_param('member_id');
  $field = get_request_param('field');
  $type = get_request_param('type');
  $value = get_request_param('value');
  $member = member_get($member_id);
  $member->update(array($field => $value));
  echo json_encode(array('value' => $value));
  exit;
}

function execute_update_user_ajax(){
  $user_id = get_request_param('user_id');
  $field = get_request_param('field');
  $type = get_request_param('type');
  $value = get_request_param('value');
  $user_obj = user_get($user_id);
  $user_obj->update(array($field => $value));
  echo json_encode(array('value' => $value));
  exit;
}