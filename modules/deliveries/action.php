<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('deliveries');

function execute_index(){
  require_once('deliveries.class.php');
  $deliveries=new Deliveries(array(),array(),-12);
  return array('deliveries'=>$deliveries);
}