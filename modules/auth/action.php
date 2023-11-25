<?php

require_once('inc.php');

function execute_login(){
  if(isset($_SESSION['user'])){
    forward_to_page('/');
  }
  //_forward/_query: see inc.php ensure_authed_user() - TODO to be implemented
  return array('email'=>get_request_param('email'), 'forward'=>get_request_param('_forward'), 'query'=>get_request_param('_query'));
}

function execute_login_ajax(){
  $error='';
  require('users.class.php');
  $users = new Users(array('email' => get_request_param('email')));
  if(empty($users)){
    $error='Unbekannte E-Mail-Adresse.';
  }else{
    $user = $users->first();
    if(!$user->password_verify(trim(get_request_param('password')))){
      $error='Falsches Passwort.';
    }
  }
  if(empty($error)){
    $user->set_session();
    logger(print_r($_SESSION['user'],1));
  }

  echo json_encode(array('error'=>$error));
  exit;
}

function execute_logout(){
  session_unset();
  forward_to_page('/');
}

function execute_password_lost(){
  $email=trim(get_request_param('email'));
  return array('email'=>$email);
}

function execute_password_lost_ajax(){
  $email=trim(get_request_param('email'));
  $message='';
  $hide_form=0;
  if(empty($email)){
    $message='Bitte E-Mail-Adresse angeben und erneut auf "Passwort zurücksetzen" klicken';
  }
  if($message==''){
    require('sql.class.php');
    $qry="SELECT * FROM msl_users WHERE email='".SQL::escapeString($email)."'";
    $user=SQL::selectOne($qry);
    if(empty($user)){
      $message='Unbekannte E-Mail-Adresse';
    }elseif(time()-strtotime($user['passwd_sent'])<120){
      $message='E-Mail mit Passwort-Setzen-Link wurde bereits vor Kurzem gesendet. Bitte in 2 Minuten erneut probieren.';
    }
  }
  if($message==''){
    $pwt=create_temp_password();
    SQL::update("UPDATE msl_users SET passwd_tmp='".SQL::escapeString($pwt)."', passwd_sent='".date('Y-m-d H:i:s')."' WHERE id='".intval($user['id'])."'");
    send_email($email,"Mit Sinn Leben eG - Passwort erneuern",
      "Dieser Link ist ca 1 Stunde gültig zum Erneuern vom Passwort:\r\n".
      "https://".$_SERVER['HTTP_HOST']."/auth/password_reset?pwt=".$pwt."\r\n");
    $message="Es wurde eine E-Mail mit Link zum Setzen eines neuen Passwortes gesendet.";
    $hide_form=1;
  }

  echo json_encode(array('message'=>$message,'hide_form'=>$hide_form));
  exit;
}

function execute_password_reset(){
  $pwt=get_request_param('pwt');
  $message='';
  $hide_form=0;
  if(empty($pwt)){
    $message='Ungültiger Link.';
    $hide_form=1;
  }
  if($message==''){
    require('sql.class.php');
    $qry="SELECT * FROM msl_users WHERE passwd_tmp='".SQL::escapeString($pwt)."'";
    $user=SQL::selectOne($qry);
    if(empty($user)){
      $message='Ungültiger Link.';
      $hide_form=1;
    }elseif(time()-strtotime($user['passwd_sent'])>3600){
      $message='Link ist nicht mehr gültig.';
      $hide_form=1;
    }
  }
  
  return array('message'=>$message,'hide_form'=>$hide_form,'pwt'=>$pwt);
}

function execute_password_set_ajax(){
  $pwt=get_request_param('pwt');
  $message='';
  $hide_form=0;
  if(empty($pwt)){
    $message='Ungültiger Aufruf.';
    $hide_form=1;
  }
  if($message==''){
    require('sql.class.php');
    $qry="SELECT * FROM msl_users WHERE passwd_tmp='".SQL::escapeString($pwt)."'";
    $user=SQL::selectOne($qry);
    if(empty($user)){
      $message='Ungültiger Aufruf.';
      $hide_form=1;
    }elseif(time()-strtotime($user['passwd_sent'])>3600){
      $message='Aufruf ist nicht mehr gültig.';
      $hide_form=1;
    }
  }
  if($message==''){
    $password=trim(get_request_param('password'));
    if(strlen($password)<4){
      $message='Passwort muss mindestens 4 Zeichen haben.';
    }
  }
  if($message=='' && $user['id']){
    $password=password_hash($password,PASSWORD_DEFAULT);
    $qry="UPDATE msl_users SET passwd_tmp='',passwd='".SQL::escapeString($password)."' WHERE id='".intval($user['id'])."'";
    SQL::update($qry);
    $message='Neues Passwort wurde gesetzt.<br>'.
      '<div class="button" onclick="location.href=\'/\';">Zur Loginseite</div>';
    $hide_form=1;
  }
  echo json_encode(array('message'=>$message,'hide_form'=>$hide_form));
  exit;
}

function create_temp_password(){
  $ret='';
  for($i=0;$i<16;$i++){
    $rt=random_int(0,2);
    if($rt==0){
      $ret.=chr(random_int(65,90)); //A-Z
    }elseif($rt==1){
      $ret.=chr(random_int(97,122)); //a-z
    }elseif($rt==2){
      $ret.=chr(random_int(48,57)); //a-z
    }
  }
  return $ret;
}

if(!function_exists('random_int')){
  function random_int($min,$max){
    return rand($min,$max);
  }
}