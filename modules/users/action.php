<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('users');
require_once('users.class.php');

function execute_index(){
  return array('users' => get_users());
}

function get_users($user_id=0){
  require_once('sql.class.php');
  $qry=
    "SELECT u.id,u.name,u.email,u.pickup_pin,a.member_id,m.name AS m_name,m.identification,a.access,a.start,a.end ".
    "FROM msl_users u ".
      "LEFT JOIN msl_access a ON (a.user_id=u.id) ".
      "LEFT JOIN msl_members m ON (a.member_id=m.id) ";
  if($user_id){
    $qry .= "WHERE u.id=".intval($user_id)." ";
  }
  $qry.=
    "ORDER BY u.name,m_name,a.access";
  $res=SQL::select($qry);
  $users=array();
  foreach($res as $v){
    $users[$v['id']]['id']=$v['id'];
    $users[$v['id']]['name']=$v['name'];
    $users[$v['id']]['email']=$v['email'];
    $users[$v['id']]['pickup_pin']=$v['pickup_pin'];
    if(!empty($v['access'])){
      $users[$v['id']]['access'][$v['member_id']]['name']=$v['m_name'];
      $users[$v['id']]['access'][$v['member_id']]['access'][$v['access']]['start']=$v['start'];
      $users[$v['id']]['access'][$v['member_id']]['access'][$v['access']]['end']=$v['end'];
    }else{
      $users[$v['id']]['access']=array();
    }
  }
  return $users;
}

function execute_edit(){
  $user_id = get_request_param('user_id');
  $users = get_users($user_id);
  #logger(print_r($users,1));
  return array(
    'user' => $users[$user_id]
  );
}

function execute_update_ajax(){
  $user_id = get_request_param('user_id');
  $field = get_request_param('field');
  $type = get_request_param('type');
  $value = get_request_param('value');
  #logger("$user_id $field $type $value");
  $user = user_get($user_id);
  $user->update(array($field => $value));
  echo json_encode(array('value' => $value));
  exit;
}

function execute_new(){

}

function execute_emails(){
  return array('users' => get_users());
}
