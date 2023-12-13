<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  require_once('products.class.php');
  $products = new Products(array('type' => array('v')));
  require_once('sql.class.php');
  $qry = "SELECT product_id, value FROM msl_preferences WHERE member_id='".intval($user['member_id'])."'";
  $preferences = SQL::selectKey2Val($qry, 'product_id', 'value');
  return array(
    'products' => $products,
    'preferences' => $preferences
  );
}

function execute_select_ajax(){
  global $user;
  $product_id = intval(get_request_param('product_id'));
  $value = intval(get_request_param('value'));
  require_once('sql.class.php');
  $qry = "INSERT INTO msl_preferences (member_id, product_id, value) VALUES ('".intval($user['member_id'])."','".intval($product_id)."','".intval($value)."') ON DUPLICATE KEY UPDATE value = VALUES(value)";
  SQL::update($qry);
  echo $value;
  exit;
}