<?php

define('QRY_MEMBER',
  "SELECT m.* ".
  "FROM msl_members m ".
  "WHERE 1=1 /*AND*/ ".
  "ORDER BY producer,name");
define('QRY_MEMBER_USERS_ACCESS',
  "SELECT a.member_id,a.user_id,u.name,u.email,GROUP_CONCAT(CONCAT_WS('|',a.access,a.start,a.end) ORDER BY 1) AS access ".
  "FROM msl_access a, msl_users u ".
  "WHERE a.member_id>0 AND a.user_id=u.id /*AND*/ ".
  "GROUP BY a.member_id,a.user_id,u.name,u.email ".
  "ORDER BY u.name");


class Member{
  public $id;
  public $name;
  public $identification;
  public $producer;
  public $consumer;

  public function __construct($member_id){
    require_once('sql.class.php');
    $qry=QRY_MEMBER;
    $qry=str_replace('/*AND*/'," AND m.id='".intval($member_id)."'",$qry);
    $res=SQL::selectOne($qry);
    $this->init_with_array($res);
  }

  public function init_with_array($array){
    unset($array['id']);
    foreach($array as $k=>$v){
      if(property_exists('Member',$k)){
        $this->{$k}=$v;
      }
    }
  }

}
