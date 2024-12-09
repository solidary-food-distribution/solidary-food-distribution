<?php

require_once('inc.php');

function execute_login(){
  if(isset($_SESSION['user'])){
    forward_to_page('/');
  }
  $scale = get_request_param('scale');
  if($scale == '1'){
    $_SESSION['scale'] = 1;
  }elseif($scale == '0'){ //explicit!
    $_SESSION['scale'] = 0;
  }
  //_forward/_query: see inc.php ensure_authed_user()
  return array(
    'email' => get_request_param('email'), 
    '_forward' => get_request_param('_forward'), 
    '_query' => get_request_param('_query')
  );
}

function execute_login_ajax(){
  $email = trim(get_request_param('email'));
  $password = trim(get_request_param('password'));
  $error='';
  if($email == '' || $password == ''){
    $error = 'Bitte E-Mail-Adresse und Passwort eingeben.';
  }
  if(empty($error)){
    require('users.class.php');
    $users = new Users(array('email' => $email));
    #logger(print_r($users, 1));
    if(empty($users->keys())){
      $error = 'Unbekannte E-Mail-Adresse.';
    }else{
      $user = $users->first();
      if(!$user->password_verify($password)){
        $error = 'Falsches Passwort.';
      }
    }
  }
  if(empty($error)){
    $user->set_session();
    #logger(print_r($_SESSION['user'],1));
  }

  echo json_encode(array('error'=>$error));
  exit;
}

function execute_login_pin_ajax(){
  $pickup_pin = get_request_param('pickup_pin');
  /*
  $pickup_pin = explode(',', $pickup_pin);
  if(count($pickup_pin)<3 || count($pickup_pin)>6){
    exit;
  }
  $pin = '';
  foreach($pickup_pin as $id){
    $id = intval($id);
    if($id<=0 || $id>32){
      exit;
    }
    $pin .= str_pad($id, 2, '0', STR_PAD_LEFT);
  }
  */
  $error = '';
  require('users.class.php');
  $users = new Users(array('pickup_pin' => $pickup_pin));
  #logger(print_r($users, 1));
  if(empty($users->keys())){
    $error = 'Unbekannte PIN';
  }
  if(empty($error)){
    $_SESSION['scale'] = 1;
    $user = $users->first();
    $user->set_session();
    #logger(print_r($_SESSION['user'],1));
  }
  echo json_encode(array('error' => $error));
  exit;
}

function execute_logout(){
  $scale = $_SESSION['scale'];
  session_unset();
  $_SESSION['scale'] = $scale;
  forward_to_page('/');
}

function execute_password_lost(){
  $email = trim(get_request_param('email'));
  $cancel = get_request_param('cancel');
  if($cancel == ''){
    $cancel = '/auth/login?email='.urlencode($email);
  }
  return array('email' => $email, 'cancel' => $cancel);
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