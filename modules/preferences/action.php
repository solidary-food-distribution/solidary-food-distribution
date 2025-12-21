<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  $return = array();
  $delivery_id = intval(get_request_param('delivery_id'));
  if($delivery_id){
    require_once('deliveries.class.php');
    $delivery = delivery_get($delivery_id);
    if($delivery){
      $return['delivery'] = $delivery;
      $product_ids=array();
      foreach($delivery->items as $item){
        $product_ids[] = $item->product->id;
      }
    }
  }
  require_once('products.class.php');
  $filter = array('type' => array('v'));
  if(isset($product_ids)){
    $filter['product_id'] = $product_ids;
  }
  $products = new Products($filter);
  $return['products'] = $products;
  $product_ids = array_keys((array)$products);
  require_once('sql.inc.php');
  $qry = "SELECT product_id, value FROM msl_preferences WHERE member_id='".intval($user['member_id'])."' AND product_id IN (".sql_escape_array($product_ids).")";
  $preferences = sql_select_key2value($qry, 'product_id', 'value');
  $return['preferences'] = $preferences;
  return $return;
}

function execute_select_ajax(){
  global $user;
  $product_id = intval(get_request_param('product_id'));
  $value = intval(get_request_param('value'));
  require_once('sql.inc.php');
  $qry = "INSERT INTO msl_preferences (member_id, product_id, value, modifier_id, modified) VALUES ('".intval($user['member_id'])."','".intval($product_id)."','".intval($value)."','".intval($user['user_id'])."',NOW()) ON DUPLICATE KEY UPDATE value = VALUES(value), modifier_id = VALUES(modifier_id), modified = VALUES(modified)";
  sql_update($qry);
  echo $value;
  exit;
}