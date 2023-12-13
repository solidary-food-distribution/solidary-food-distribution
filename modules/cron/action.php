<?php

require_once('inc.php');

function execute_run(){
  global $user;
  $user = array('user_id' => 0); //for SQL::update-logger
  require_once('sql.class.php');
  $qry = "SELECT * FROM msl_crons WHERE next_run<='".date('Y-m-d H:i:s')."' ORDER BY next_run";
  $crons = SQL::select($qry);
  foreach($crons as $cron){
    if(substr($cron['task'],0,5) == 'cron_' && function_exists($cron['task'])){
      call_user_func($cron['task']);
      $qry = "UPDATE msl_crons SET last_run='".date('Y-m-d H:i:s')."', next_run='".date('Y-m-d H:i:s', time() + 60*intval($cron['minutes_interval']))."' WHERE cron_id='".$cron['cron_id']."'";
      SQL::update($qry);
    }
  }
  echo "1";
  exit;
}


function cron_may_send_mail_preferences(){
  logger("cron_may_send_mail_preferences");
}