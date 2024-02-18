<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  if($_SESSION['scale']){
    if(user_has_access('pickups') && !user_has_access('deliveries') && !user_has_access('inventory')){
      forward_to_page('/pickups');
    }else{
      forward_to_page('/store');
    }
  }
}

function execute_noaccess(){
}