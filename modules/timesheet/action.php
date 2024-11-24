<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  $month=get_request_param('month');
  $edit_id=get_request_param('edit_id');
  if($month==''){
    $month=date('Y-m');
  }
  require_once('sql.class.php');
  $qry=
    "SELECT * ".
    "FROM msl_timesheet ".
    "WHERE user_id='".intval($user['user_id'])."' ".
      "AND `date` LIKE '".SQL::escapeString($month)."%'".
    "ORDER BY `date`,modified";
  $timesheet=SQL::selectID($qry,'id');

  $months=array();
  $yn=date('Y');
  $mn=date('m');
  for($y=2023;$y<=$yn;$y++){
    for($m=1;(($y<$yn && $m<=12) || $m<=$mn);$m++){
      $key=$y.'-'.str_pad($m,2,'0',STR_PAD_LEFT);
      $months[$key]=translate_month($m).' '.$y;
    }
  }

  if($month<='2023-01'){
    $month_prev='';
  }else{
    $month_prev=date('Y-m',strtotime($month)-60*60*24-10);
  }
  if($month>=date('Y-m')){
    $month_next='';
  }else{
    $month_next=date('Y-m',strtotime($month)+60*60*24*40);
  }

  if($edit_id=='new'){
    if($month==date('Y-m')){
      $date=date('Y-m-d');
    }elseif(!empty($timesheet)){
      $date=end($timesheet)['date'];
    }else{
      $date=$month.'-01';
    }
    $timesheet['new']=array(
      'user_id'=>$user['user_id'],
      'id'=>'new',
      'date'=>$date,
      'mins'=>'',
      'km'=>'',
      'topic'=>'',
      'what'=>'',
      'modified'=>''
    );
  }

  return array('timesheet'=>$timesheet,'months'=>$months,'month'=>$month,'month_prev'=>$month_prev,'month_next'=>$month_next,'edit_id'=>$edit_id);
}

function execute_new(){
  $month=get_request_param('month');
  if($month==''){
    $month=date('Y-m');
    set_request_param('month',$month);
  }
  set_request_param('edit_id','new');
  $return=execute_index();
  $return['template']='index.php';
  return $return;
}

function execute_edit_ajax(){
  global $user;
  $id=intval(get_request_param('id'));
  require_once('sql.class.php');
  $qry="SELECT `date` FROM msl_timesheet WHERE user_id='".intval($user['user_id'])."' AND id='".SQL::escapeString($id)."'";
  $date=SQL::selectOne($qry)['date'];
  $month=substr($date,0,7);
  set_request_param('month',$month);
  set_request_param('edit_id',$id);
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}

function execute_save_ajax(){
  global $user;
  $id=get_request_param('id');
  $date=get_request_param('date');
  $mins=get_request_param('mins');
  $km=get_request_param('km');
  $topic=get_request_param('topic');
  $what=trim(get_request_param('what'));

  require_once('sql.class.php');
  if(intval($mins) || intval($km) || !empty($what)){
    if(!intval($id)){
      $id=get_unix_ms();
      $modified=$id;
    }else{
      $modified=get_unix_ms();
    }
    $qry=
      "INSERT INTO msl_timesheet (user_id,id,`date`,mins,km,topic,what,modified) ".
      "VALUES ('".intval($user['user_id'])."','".intval($id)."','".SQL::escapeString($date)."','".intval($mins)."','".intval($km)."','".SQL::escapeString($topic)."','".SQL::escapeString($what)."','".intval($modified)."') ".
      "ON DUPLICATE KEY UPDATE `date`=VALUES(`date`),mins=VALUES(mins),km=VALUES(km),topic=VALUES(topic),what=VALUES(what),modified=VALUES(modified)";
    SQL::update($qry);
  }elseif(intval($id)){
    SQL::update("DELETE FROM msl_timesheet WHERE user_id='".intval($user['user_id'])."' AND id='".intval($id)."'");
  }

  $month=substr($date,0,7);
  set_request_param('month',$month);

  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}

function get_unix_ms(){
  return round(microtime(1)*1000);
}