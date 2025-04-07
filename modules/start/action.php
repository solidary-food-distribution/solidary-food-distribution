<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  if(isset($_SESSION['scale']) && $_SESSION['scale']){
    if(user_has_access('pickups') && !user_has_access('deliveries') && !user_has_access('inventory')){
      forward_to_page('/pickups');
    }else{
      forward_to_page('/start/store');
    }
  }
  require_once('infos.class.php');
  $infos = new Infos(array('published!=' => '0000-00-00 00:00:00'));
  require_once('info_users.class.php');
  $info_users = new InfoUsers(array('user_id' => $user['user_id'], 'read!=' => '0000-00-00 00:00:00'));
  foreach($info_users as $info_user){
    if(isset($infos[$info_user->info_id])){
      unset($infos[$info_user->info_id]);
    }
  }
  return array('infos' => $infos);
}

function execute_info_read_ajax(){
  global $user;
  $info_id = get_request_param('info_id');
  require_once('info_users.class.php');
  $info_users = new InfoUsers(array('info_id' => $info_id, 'user_id' => $user['user_id']));
  if(!$info_users->count()){
    $info_user = InfoUser::create($info_id, $user['user_id']);
  }else{
    $info_user = $info_users->first();
  }
  $info_user->update(array('read' => date('Y-m-d H:i:s')));
  echo json_encode(array('result'=>1));
  exit;
}

function execute_store(){
}

function execute_version(){
}

function execute_noaccess(){
}