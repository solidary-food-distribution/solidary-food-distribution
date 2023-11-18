<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('members');

function execute_index(){
  require_once('member.class.php');
  require_once('sql.class.php');

  $qry=QRY_MEMBER;
  $members=SQL::selectID($qry,'id');

  $qry=QRY_MEMBER_USERS_ACCESS;
  $res=SQL::selectID2($qry,'member_id','user_id');
  foreach($members as $member_id=>$member){
    $members[$member_id]['access_users']=array();
    if(isset($res[$member_id])){
      foreach($res[$member_id] as $user_id=>$user){
        $members[$member_id]['access_users'][$user_id]['name']=$user['name'];
        $members[$member_id]['access_users'][$user_id]['email']=$user['email'];
        $access=explode(',',$user['access']);
        foreach($access as $a){
          $a=explode('|',$a);
          $members[$member_id]['access_users'][$user_id]['access'][$a[0]]['start']=$a[1];
          $members[$member_id]['access_users'][$user_id]['access'][$a[0]]['end']=$a[2];
        }
      }
    }
  }
  return array('members'=>$members);

}

function execute_new(){

}
