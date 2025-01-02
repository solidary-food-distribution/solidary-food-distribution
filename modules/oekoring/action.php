<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
}

function execute_products(){
}

function execute_products_import(){
  require_once('oekoring.inc.php');
  $result = oekoring_download_bnns();
  print_r($result);
  $result = oekoring_import_bnns();
  echo '<pre>';
  print_r($result);
  exit;
}
