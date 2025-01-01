<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('deliveries');

function execute_index(){
  require_once('deliveries.class.php');
  $ds = new Deliveries(array(), array('id' => 'DESC') , 0, 6);
  $deliveries = array();
  foreach($ds as $id => $d){
    $deliveries[$id] = $d;
  }
  $deliveries = array_reverse($deliveries, true);

  require_once('delivery_items.class.php');
  $dis = new DeliveryItems(array('delivery_id' => array_keys($deliveries)));
  $delivery_items = array();
  foreach($dis as $di){
    if($di->amount_pieces || $di->amount_weight){
      $delivery_items[$di->delivery_id][$di->id] = $di;
    }
  }
  $product_ids = $dis->get_product_ids();

  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));

  require_once('members.class.php');
  $suppliers = new Members(array('producer' => array(1,2)));

  return array('deliveries'=>$deliveries, 'delivery_items' => $delivery_items, 'suppliers' => $suppliers, 'products' => $products);
}