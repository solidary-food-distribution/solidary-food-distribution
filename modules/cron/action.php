<?php

require_once('inc.php');

function execute_run(){
  global $user;
  $user = array('user_id' => 1); //for SQL::update-logger
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
  exit;
}


function cron_may_send_purchases(){
  update_delivery_dates();

}

function update_delivery_dates(){
  require_once('sql.class.php');
  $qry = "SELECT * FROM msl_delivery_dates WHERE `date`>=CURDATE() ORDER BY `date`";
  $delivery_dates = SQL::select($qry);

  $qry = "SELECT id, purchase_time FROM msl_members WHERE producer>0 AND status='a' AND purchase_time!=''";
  $supplier_purchase_times = SQL::selectKey2Val($qry, 'id', 'purchase_time');

  foreach($delivery_dates as $delivery_date){
    $qry = "INSERT INTO msl_purchases (delivery_date_id, supplier_id, `datetime`) VALUES ";
    foreach($supplier_purchase_times as $supplier_id => $purchase_time){
      $qry .= "('".intval($delivery_date['id'])."', '".intval($supplier_id)."', ";
      $purchase_datetime = date('Y-m-d H:i:s', strtotime($purchase_time, strtotime($delivery_date['date'])));
      $qry .= "'".$purchase_datetime."'),";
    }
    $qry = rtrim($qry, ',')." ON DUPLICATE KEY UPDATE `datetime`=VALUES(`datetime`)";
    SQL::update($qry);
  }
}

