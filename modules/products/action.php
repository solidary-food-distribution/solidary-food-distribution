<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('products');

require_once('products.class.php');

function execute_index(){
  $products = new Products(array('type' => array('p', 'k', 'b')));
  return array(
    'products' => $products,
  );
}

function execute_edit(){
  $product_id = get_request_param('product_id');
  $product = product_get($product_id);
  return array(
    'product' => $product,
  );
}

function execute_update_ajax(){
  $product_id = get_request_param('product_id');
  $field = get_request_param('field');
  $type = get_request_param('type');
  $value = get_request_param('value');
  if($type == 'weight'){
    $value = str_replace(',', '.', $value);
    $value = number_format(floatval($value), 3, '.', '');
  }elseif($type == 'pieces'){
    $value = str_replace(',', '.', $value);
    $value = number_format(floatval($value), 2, '.', '');
  }elseif($type == 'money'){
    $value = str_replace(',', '.', $value);
    $value = number_format(floatval($value), 2, '.', '');
  }
  $product = product_get($product_id);
  
  $updates = array($field => $value);
  $product->update($updates);
  echo json_encode(array('value' => $value));
  exit;
}
