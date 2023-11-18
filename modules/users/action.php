<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('users');

function execute_index(){
  require_once('sql.class.php');
  $qry=
    "SELECT u.id,u.name,u.email,a.member_id,m.name AS m_name,m.identification,a.access,a.start,a.end ".
    "FROM msl_users u ".
      "LEFT JOIN msl_access a ON (a.user_id=u.id) ".
      "LEFT JOIN msl_members m ON (a.member_id=m.id) ".
    "ORDER BY u.name,m_name,a.access";
  $res=SQL::select($qry);
  $users=array();
  foreach($res as $v){
    $users[$v['id']]['name']=$v['name'];
    $users[$v['id']]['email']=$v['email'];
    if(!empty($v['access'])){
      $users[$v['id']]['access'][$v['member_id']]['name']=$v['m_name'];
      $users[$v['id']]['access'][$v['member_id']]['identification']=$v['identification'];
      $users[$v['id']]['access'][$v['member_id']]['access'][$v['access']]['start']=$v['start'];
      $users[$v['id']]['access'][$v['member_id']]['access'][$v['access']]['end']=$v['end'];
    }else{
      $users[$v['id']]['access']=array();
    }
  }
  return array('users'=>$users);

}

function execute_new(){

}
