<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){

}

function execute_new(){
  $delivery_id=get_request_param('delivery_id');
  $return=array();
  if(intval($delivery_id)){
    require_once('deliveries.inc.php');
    $return['delivery']=delivery_get($delivery_id);
  }
  return $return;
}
