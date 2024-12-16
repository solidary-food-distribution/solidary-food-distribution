<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  return array('user' => $user);
}

function execute_show_pin_ajax(){
  global $user;
  $pin = '<div class="input">'.$user['pickup_pin'].'</div>';
  if(empty($pin)){
    $pin = "[noch nicht gesetzt, bitte an info@mit-sinn-leben.de eine Email senden]";
  }
  echo $pin;
  exit;
}
