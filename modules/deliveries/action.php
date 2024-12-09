<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('deliveries');

function execute_index(){
  require_once('deliveries.class.php');
  $deliveries=new Deliveries(array(),array(),-6);

  $product_ids = array('0'=>0);
  foreach($deliveries as $delivery){
    foreach($delivery->items as $item){
      $product_ids[$item->product_id] = 1;
    }
  }
  require_once('products.class.php');
  $products = new Products(array('id' => array_keys($product_ids)));

  require_once('members.class.php');
  $suppliers = new Members(array('producer' => array(1,2)));
  return array('deliveries'=>$deliveries, 'suppliers' => $suppliers, 'products' => $products);
}