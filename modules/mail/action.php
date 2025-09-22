<?php

require_once('inc.php');

function execute_answer(){
  $user = $_SESSION['user'];
  $a = get_request_param('a');
  $as = explode('_', $a);
  $mail_id = $as[0];
  $value = $as[1];
  $user_id = $as[2];
  $crc = $as[3];
  $check = dechex(crc32(hash('sha256',$mail_id.'#{zU'.$value.'#g&T$c}'.$user_id)));
  #logger("crc|$crc|check|$check");
  if($crc == $check){
    require('sql.class.php');
    $qry="INSERT INTO msl_mail_answers (mail_id, answer_id, user_id) VALUES ('".intval($mail_id)."','".intval($value)."','".intval($user_id)."')";
    SQL::insert($qry);
    $message = "Danke fürs Bestätigen der E-Mail.";
    if($user_id != $user['user_id']){
      logger("USER_ID WEICHT AB angemeldet:".$user['user_id']." link:".$user_id." answer: ".$a);
    }
  }else{
    $message = "Der Link scheint kaputt zu sein.";
    logger("KAPUTTER LINK: ".$a);
  }
  return array('message' => $message);
}

