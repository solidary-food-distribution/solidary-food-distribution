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

function execute_products(){
  if(!user_has_access('products')){
    forward_to_noaccess();
  }
  $supplier_id = get_request_param('supplier_id');
  if(!$supplier_id){
    forward_to_page('/admin/products_suppliers');
  }
  $status = get_request_param('status');
  require_once('members.class.php');
  $suppliers = new Members(array('id' => $supplier_id, 'producer>' => '0'));
  $supplier = $suppliers->first();

  require_once('products.class.php');
  $filter = array('supplier_id' => $supplier_id);
  if($status !== ''){
    $filter['status'] = $status;
  }
  $products = new Products($filter);

  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $products->keys()));

  return array('supplier' => $supplier, 'products' => $products, 'prices' => $prices, 'status' => $status);
}

function execute_products_filter_ajax(){
  $field = get_request_param('field');
  $value = get_request_param('value');
  set_request_param($field, $value);
  $return = execute_products();
  $return['template'] = 'products.php';
  $return['layout'] = 'layout_null.php';
  return $return;
}

function execute_products_update_ajax(){
  $product_id = get_request_param('product_id');
  $field = get_request_param('field');
  $value = get_request_param('value');

  if(strpos(',purchase,price', $field)){
    $value = str_replace(',', '.', $value);
    require_once('prices.class.php');
    $prices = new Prices(array('product_id' => $product_id, 'start<=' => date('Y-m-d'), 'end>=' => date('Y-m-d')));
    foreach($prices as $price){
      $price->update(array($field => $value));
    }
  }else{
    require_once('products.class.php');
    $product = Products::sget($product_id);
    $product->update(array($field => $value));
  }
  echo json_encode(array('result' => '1'));
  exit();
}

function execute_products_suppliers(){
  require_once('members.class.php');
  $suppliers = new Members(array('producer>' => '0'));
  return array('suppliers' => $suppliers);
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

function execute_orders(){
  if(!user_has_access('orders')){
    forward_to_noaccess();
  }
  $date = get_request_param('date');
  if($date == ''){
    $date = date('Y-m-d',strtotime('-3 DAYS',time()));
  }
  
  require_once('delivery_dates.class.php');
  $delivery_dates = new DeliveryDates(array('date>=' => $date), array('date' => 'ASC'), 0, 1);
  $delivery_date = $delivery_dates->first();
  $date = $delivery_date->date;

  $delivery_dates = new DeliveryDates(array('date<' => $date), array('date' => 'DESC'), 0, 1);
  $delivery_date = $delivery_dates->first();
  $date_prev = $delivery_date->date;

  $delivery_dates = new DeliveryDates(array('date>' => $date), array('date' => 'ASC'), 0, 1);
  $delivery_date = $delivery_dates->first();
  $date_next = $delivery_date->date;

  #logger("date $date_prev $date $date_next");

  $orders_next = $date_next;
  if(!$orders_next){
    $orders_next=date('Y-m-d',strtotime('+1 DAYS',strtotime($date)));
  }

  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date>=' => $date, 'pickup_date<' => $orders_next));
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $orders->keys()));
  $product_ids = $order_items->get_product_ids();
  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));

  $supplier_ids = array();
  $member_orders = array();
  $order_items_array = array();
  foreach($order_items as $order_item){
    if($order_item->amount_pieces == 0 && $order_item->amount_weight == 0){
      continue;
    }
    $order = $orders[$order_item->order_id];
    $member_orders[$order->member_id][$order->id] = $order;
    $product_id = $order_item->product_id;
    $supplier_id = $products[$product_id]->supplier_id;
    $supplier_ids[$supplier_id] = 1;
    $order_items_array[$order->id][$supplier_id.' '.$products[$product_id]->name] = $order_item;
  }

  require_once('pickups.class.php');
  $pickups = new Pickups(array('created>' => $date, 'created<' => $orders_next));
  require_once('pickup_items.class.php');
  $pickup_items = new PickupItems(array('pickup_id' => $pickups->keys()));
  $pui_product_ids = $pickup_items->get_product_ids();
  $pui_product_ids = array_diff($pui_product_ids, $product_ids);
  #logger(print_r($pui_product_ids,1));
  $pickup_items_array = array();
  foreach($pickup_items as $pickup_item){
    $id = $pickup_item->order_item_id;
    if(!$id){
      'noi'.$pickup_item->id;
    }
    $pickup_items_array[$id]=$pickup_item;
  }

  require_once('members.class.php');
  $suppliers = new Members(array('id' => array_keys($supplier_ids)));
  $members = new Members(array('id' => array_keys($member_orders)));

  return array('date' => $date, 'date_prev' => $date_prev, 'date_next' => $date_next, 'member_orders' => $member_orders, 'members' => $members, 'order_items_array' => $order_items_array, 'pickup_items_array' => $pickup_items_array, 'products' => $products, 'suppliers' => $suppliers);
}