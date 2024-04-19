<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('members');

require_once('members.class.php');
require_once('users.class.php');

function execute_index(){
  $members = new Members();
  $users = array();
  foreach(new Users() as $user){
    if(!isset($users[$user->member_id])){
      $users[$user->member_id] = array();
    }
    $users[$user->member_id][] = $user;
  }
  return array(
    'members' => $members,
    'users' => $users
  );
}

function execute_edit(){
  $member_id = get_request_param('member_id');
  $member = member_get($member_id);
  return array(
    'member' => $member,
  );
}

function execute_new(){
  $member_id = Members::create('.Neues Mitglied');
  $user_id = Users::create('Email setzen '.$member_id, '.Neuer Benutzer', $member_id);
  require_once('sql.class.php');
  $qry = "INSERT INTO msl_access (user_id, access, member_id, start, end) VALUES ".
    "($user_id, 'access', $member_id, '0000-00-00', '9999-12-31'),".
    "($user_id, 'order', $member_id, '0000-00-00', '9999-12-31'),".
    "($user_id, 'pickups', $member_id, '0000-00-00', '9999-12-31'),".
    "($user_id, 'preferences', $member_id, '0000-00-00', '9999-12-31')";
  SQL::insert($qry);
  forward_to_page('/members/edit', 'member_id='.$member_id);
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
