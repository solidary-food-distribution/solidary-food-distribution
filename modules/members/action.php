<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('members');

require_once('members.class.php');

function execute_index(){
  $members = new Members();
  return array('members' => $members);
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
