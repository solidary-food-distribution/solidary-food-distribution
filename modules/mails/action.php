<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('mails');


require_once('mails.class.php');

function execute_index(){
  $mails = new Mails(array(), array('id' => 'DESC'), 0, 10);
  return array(
    'mails' => $mails,
  );
}

function execute_new(){
  $mail = Mail::create("", "Neue E-Mail", "");
  forward_to_page('/mails/mail?mail_id='.$mail->id);
}

function execute_mail(){
  $mail_id = get_request_param('mail_id');
  $mail = Mails::sget($mail_id);
  return array(
    'mail' => $mail,
  );
}

function execute_update_ajax(){
  $mail_id = get_request_param('mail_id');
  $field = get_request_param('field');
  $type = get_request_param('type');
  $value = get_request_param('value');
  $mail = Mails::sget($mail_id);
  $mail->update(array($field => $value));
  echo json_encode(array('value' => $value));
  exit;
}

function execute_send_ajax(){
  $mail_id = get_request_param('mail_id');
  $mail = Mails::sget($mail_id);
  $error = '';
  if($mail->to == '' ){
    $error .= 'Bitte An angeben! ';
  }
  if($mail->subject == ''){
    $error .= 'Bitte Betreff angeben! ';
  }
  if($mail->content == ''){
    $error .= 'Bitte Inhalt angeben! ';
  }
  if(empty($error)){
    $tos = array();
    if($mail->to == '[ALL_USERS]'){
      require_once('users.class.php');
      $users = new Users();
      foreach($users as $u){
        if(strpos(' '.$u->name, 'GEKÜNDIGT') || strpos(' '.$u->email, 'GEKÜNDIGT') || !strpos($u->email, '@')){
          continue;
        }
        $tos[$u->id] = $u->email;
      }
    }else{
      $tos[] = $mail->to;
    }
    foreach($tos as $user_id => $to){
      $content = send_set_content_urls($mail->id, $mail->content, $user_id);
      send_email($to, $mail->subject, $content);
    }
    $mail->update(array('sent' => date('Y-m-d H:i:s')));
  }
  echo json_encode(array('error' => $error));
  exit;
}
function send_set_content_urls($mail_id, $content, $user_id){
  $loop_break = 10;
  while((strpos($content, '[button ') !== false) && ($loop_break > 0)){
    $button = get_between_markers($content, '[button ', '"]');
    $value = get_between_markers($button, 'value="', '"');
    $label = get_between_markers($button, 'label="', '"');
    #logger("button|$button|value|$value|label|$label|");
    $url = "https://".$_SERVER['HTTP_HOST']."/mail/answer?a=".$mail_id."_".$value."_".$user_id."_".dechex(crc32(hash('sha256',$mail_id.'#{zU'.$value.'#g&T$c}'.$user_id)));
    $content = str_replace('[button '.$button.']', $url, $content);
    #logger($content);
    #break;
    $loop_break--;
  }
  return $content;
}
function get_between_markers($str, $start, $end){
  $pos_start = strpos($str, $start);
  $pos_end = strpos($str, $end, $pos_start + strlen($start));
  $return = substr($str, $pos_start + strlen($start), $pos_end - $pos_start - strlen($start) + strlen($end) - 1);
  return $return;
}