<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  if(isset($_SESSION['scale']) && $_SESSION['scale']){
    if(user_has_access('pickups') && !user_has_access('deliveries') && !user_has_access('inventory')){
      forward_to_page('/pickups');
    }else{
      forward_to_page('/start/store');
    }
  }
}

function execute_store(){
}

function execute_version(){
}

function execute_noaccess(){
}