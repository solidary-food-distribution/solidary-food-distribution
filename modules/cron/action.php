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
  echo "1";
  exit;
}


function cron_may_send_mail_preferences(){
  require_once('sql.class.php');
  $qry = "SELECT ".
      "d.id, d.status, ".
      "MAX(di.modified) AS modified ".
    "FROM msl_deliveries d, msl_delivery_items di, msl_products p ".
    "WHERE d.id = di.delivery_id AND di.product_id = p.pid ".
      "AND d.created >= '".date('Y-m-d H:i:s', time() - 60*60*24*7)."' ".
      "AND d.preferences_mail_sent = '0000-00-00 00:00:00' ".
      "AND p.type = 'v' ".
    "GROUP BY d.id, d.status ".
    "HAVING (modified < '".date('Y-m-d H:i:s', time() - 60*10)."' OR status != 'o') ".
    "ORDER BY modified";
  $ds = SQL::select($qry);
  foreach($ds as $d){
    if($d['status'] == 'o'){
      SQL::update("UPDATE msl_deliveries SET status='a' WHERE id='".intval($d['id'])."'");
    }
    cron_send_mail_preferences($d['id']);
    SQL::update("UPDATE msl_deliveries SET preferences_mail_sent=NOW() WHERE id='".intval($d['id'])."'");
  }
}

function cron_send_mail_preferences($delivery_id){
  require_once('deliveries.class.php');
  $delivery = delivery_get($delivery_id);
  $now = format_date(date('Y-m-d H:i:s'));
  $subject = "Neue Lieferung von ".$delivery->supplier->name." - bitte Präferenzen angeben - ".$now;

  require_once('sql.class.php');
  $qry = "SELECT ".
      "u.id, u.email, u.name, ".
      "GROUP_CONCAT(m.name) member_names ".
    "FROM msl_users u, msl_access a, msl_members m ".
    "WHERE u.id = a.user_id AND a.access = 'preferences' AND a.member_id = m.id AND m.consumer = 1 ".
      "AND a.start <= CURDATE() AND a.end >= CURDATE() ".
      "AND m.id IN (SELECT o.member_id FROM msl_orders o, msl_products p WHERE o.amount>0 AND o.pid = p.pid AND p.type='b' AND p.producer_id='".intval($delivery->supplier->id)."') ".
    "GROUP BY u.id, u.email, u.name";
  $us = SQL::select($qry);
  foreach($us as $u){
    $text = "Liebe/r ".$u['name'].",\r\n\r\n".
      "von unserer/m geschätzten Erzeuger ".$delivery->supplier->name." haben wir folgende Lebensmittel erhalten:\r\n";
    foreach($delivery->items as $item){
      $text .= "- ".$item->product->name."\r\n";
    }
    $text .= "\r\nBitte gebe Präferenzen für diese Abholung an:\r\n";
    $text .= "https://".$_SERVER['HTTP_HOST']."/preferences?delivery_id=".$delivery_id."\r\n";
    send_email($u['email'], $subject, $text);
  }
}