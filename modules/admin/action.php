<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('admin');


function execute_index(){
  $products = user_has_access('products');
  $members = user_has_access('members');
  $users = user_has_access('users');
  $orders = user_has_access('orders');
  $purchases = user_has_access('purchases');
  $debits = user_has_access('debits');
  $remote = user_has_access('remote');
  $mails = user_has_access('mails');
  if(!$products && !$members && !$users && !$orders && !$purchases && !$debits && !$remote && !$mails){
    forward_to_noaccess();
  }
  return array(
    'products' => $products,
    'members' => $members,
    'users' => $users,
    'orders' => $orders,
    'purchases' => $purchases,
    'debits' => $debits,
    'remote' => $remote,
    'mails' => $mails,
  );
}

function execute_purchases(){
  if(!user_has_access('purchases')){
    forward_to_noaccess();
  }

  $mindate = date('Y-m-d', strtotime('-21 DAYS', time()));
  require_once('purchases.class.php');
  $purchases = new Purchases(array('datetime>=' => $mindate));
  $member_ids = array();
  $delivery_date_ids = array();
  foreach($purchases as $purchase){
    $member_ids[$purchase->supplier_id] = 1;
    $delivery_date_ids[$purchase->delivery_date_id] = 1;
  }

  require_once('members.class.php');
  $suppliers = new Members(array('id' => array_keys($member_ids)));

  require_once('delivery_dates.class.php');
  $delivery_dates = new DeliveryDates(array('id' => array_keys($delivery_date_ids)));

  return array('purchases' => $purchases, 'delivery_dates' => $delivery_dates, 'suppliers' => $suppliers);
}

function execute_purchase(){
  if(!user_has_access('purchases')){
    forward_to_noaccess();
  }
  $purchase_id = get_request_param('purchase_id');
  require_once('purchases.class.php');
  $purchase = Purchases::sget($purchase_id);

  require_once('delivery_dates.class.php');
  $delivery_date = DeliveryDates::sget($purchase->delivery_date_id);

  require_once('purchases.inc.php');
  $product_sums = purchases_get_product_sums($delivery_date->date, $purchase->supplier_id);
  logger("product_sums ".print_r($product_sums,1));

  require_once('products.class.php');
  $products = new Products(array('id' => array_keys($product_sums)));

  require_once('members.class.php');
  $supplier = Members::sget($purchase->supplier_id);

  return array('purchase' => $purchase, 'delivery_date' => $delivery_date, 'product_sums' => $product_sums, 'products' => $products, 'supplier' => $supplier);
}

function execute_orders_csv(){
  if(!user_has_access('orders')){
    forward_to_noaccess();
  }
  $supplier_id = get_request_param('supplier_id');

  require_once('members.class.php');
  $supplier = member_get($supplier_id);

  $pickup_date = '2025-01-24';
  $product_sums = orders_get_product_sums($pickup_date);

  return array('product_sums' => $product_sums, 'supplier' => $supplier, 'pickup_date' => $pickup_date);

}


function execute_orders(){
  if(!user_has_access('orders')){
    forward_to_noaccess();
  }
  #require_once('sql.class.php');
  #$qry = "SELECT pickup_date, count(*) ...
  $pickup_date = '2025-01-24';
  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date' => $pickup_date));
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $orders->keys()));
  $product_ids = $order_items->get_product_ids();
  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));

  $product_sums = array();
  $supplier_ids = array();
  foreach($order_items as $order_item){
    if($order_item->amount_pieces == 0 && $order_item->amount_weight == 0){
      continue;
    }
    $product_id = $order_item->product_id;
    $member_id = $orders[$order_item->order_id]->member_id;
    $supplier_id = $products[$product_id]->supplier_id;
    $supplier_ids[$supplier_id] = 1;
    $product_sums[$member_id][$supplier_id] += 1;
  }

  ksort($product_sums);

  require_once('members.class.php');
  $suppliers = new Members(array('id' => array_keys($supplier_ids)));
  $members = new Members(array('id' => array_keys($product_sums)));

  return array('members' => $members, 'product_sums' => $product_sums, 'suppliers' => $suppliers);
}