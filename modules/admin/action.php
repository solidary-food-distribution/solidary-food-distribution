<?php

require_once('inc.php');
user_ensure_authed();
user_has_access('admin');


function execute_index(){
  $products=user_has_access('products');
  $members=user_has_access('members');
  $users=user_has_access('users');
  $orders=user_has_access('orders');
  $debits=user_has_access('debits');
  $remote=user_has_access('remote');
  if(!$products && !$members && !$users && !$orders && !$debits && !$remote){
    forward_to_noaccess();
  }
  return array(
    'products'=>$products,
    'members'=>$members,
    'users'=>$users,
    'orders'=>$orders,
    'debits'=>$debits,
    'remote'=>$remote,
  );
}

function execute_orders(){
  if(!user_has_access('orders')){
    forward_to_noaccess();
  }
  #require_once('sql.class.php');
  #$qry = "SELECT pickup_date, count(*) ...
  $pickup_date = '2024-12-06';
  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date' => $pickup_date));
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $orders->keys()));
  $product_ids = $order_items->get_product_ids();
  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));
  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $product_ids, 'start<=' => $pickup_date, 'end>=' => $pickup_date));

  $product_sums = array();
  foreach($order_items as $order_item){
    $product_id = $order_item->product_id;
    $supplier_id = $products[$product_id]->supplier_id;
    $product_sums[$supplier_id][$product_id]['amount_pieces'] += $order_item->amount_pieces;
    $product_sums[$supplier_id][$product_id]['amount_weight'] += $order_item->amount_weight;
  }

  ksort($product_sums);

  require_once('members.class.php');
  $suppliers = new Members(array('id' => array_keys($product_sums)));
  return array('products' => $products, 'prices' => $prices, 'product_sums' => $product_sums, 'suppliers' => $suppliers);
}