<?php

require_once('inc.php');
user_ensure_authed();
#user_needs_access('polls');

function execute_index(){
  global $user;

  $date = get_request_param('date');

  require_once('infos.class.php');
  $infos = new Infos(array('published!=' => '0000-00-00 00:00:00'),array('published' => 'DESC'));
  if($date == ''){
    $date = substr($infos->first()->published,0, 10);
  }

  $dates = array();
  $date_prev = '';
  $date_next = '';
  $infos_array = array();
  foreach($infos as $info){
    if(!empty($infos_array) && empty($date_prev) && substr($infos_array[0]->published, 0, 10) > substr($info->published, 0, 10)){
      $date_prev = substr($info->published, 0, 10);
    }
    if($date == substr($info->published, 0, 10)){
      $infos_array[] = $info;
    }
    if(empty($infos_array)){
      $date_next = substr($info->published, 0, 10);
    }
    $dates[substr($info->published, 0, 10)] = format_date($info->published);
  }
  return array('infos' => $infos_array, 'dates' => $dates, 'date' => $date, 'date_prev' => $date_prev, 'date_next' => $date_next);
}

