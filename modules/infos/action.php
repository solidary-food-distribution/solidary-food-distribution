<?php

require_once('inc.php');
user_ensure_authed();
#user_needs_access('polls');

function execute_index(){
  global $user;
  require_once('infos.class.php');
  $infos = new Infos(array('published!=' => '0000-00-00 00:00:00'));
  return array('infos' => $infos);
}

