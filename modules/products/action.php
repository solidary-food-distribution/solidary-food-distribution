<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('products');

require_once('products.class.php');

function execute_index(){
  $products = new Products(array('type' => array('p', 'k', 'b'), 'supplier_id' => array(20)));
  $product_ids = $products->keys();
  if(empty($product_ids)){
    $product_ids=array('0');
  }

  require_once('prices.class.php');
  $date = date('Y-m-d');
  $prices = new Prices(array('product_id' => $product_ids, 'start<=' => $date, 'end>=' => $date));

  return array(
    'products' => $products,
    'prices' => $prices,
  );
}

function execute_edit(){
  $product_id = get_request_param('product_id');
  if($product_id == 'new'){
    $product = Product::create(array('supplier_id' => 20));
  }else{
    $products = new Products(array('product_id' => $product_id));
    $product = $products->first();
  }
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
    $value = number_format(floatval($value), 3, '.', '');
  }
  $product = product_get($product_id);
  
  $updates = array($field => $value);
  $product->update($updates);
  echo json_encode(array('value' => $value));
  exit;
}
