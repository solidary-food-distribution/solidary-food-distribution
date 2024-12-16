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
  $pickup_date = '2024-12-20';
  $product_sums = orders_get_product_sums($pickup_date);

  require_once('members.class.php');
  $suppliers = new Members(array('id' => array_keys($product_sums)));
  return array('product_sums' => $product_sums, 'suppliers' => $suppliers);
}

function execute_orders_csv(){
  if(!user_has_access('orders')){
    forward_to_noaccess();
  }
  $supplier_id = get_request_param('supplier_id');

  require_once('members.class.php');
  $supplier = member_get($supplier_id);

  $pickup_date = '2024-12-20';
  $product_sums = orders_get_product_sums($pickup_date);

  return array('product_sums' => $product_sums, 'supplier' => $supplier, 'pickup_date' => $pickup_date);

}


function orders_get_product_sums($pickup_date){
  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date' => $pickup_date));
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $orders->keys()));
  $product_ids = $order_items->get_product_ids();

  $product_ids = array_flip($product_ids);
  require_once('sql.class.php');
  $qry = "select min(o.pickup_date) minpud,max(o.pickup_date) maxpud,oi.product_id,sum(oi.amount_pieces) summe,p.amount_per_bundle from msl_orders o,msl_order_items oi, msl_products p where o.id=oi.order_id and oi.product_id=p.id and p.supplier_id=35 and oi.amount_pieces>0 and p.status='o' group by oi.product_id,p.amount_per_bundle having minpud='2024-12-06' and summe<amount_per_bundle";
  $res = SQL::select($qry);
  foreach($res as $row){
    unset($product_ids[$row['product_id']]);
  }
  $product_ids = array_keys($product_ids);

  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));
  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $product_ids, 'start<=' => $pickup_date, 'end>=' => $pickup_date));

  $product_sums = array();
  foreach($order_items as $order_item){
    if($order_item->amount_pieces == 0 && $order_item->amount_weight == 0){
      continue;
    }
    if(!in_array($order_item->product_id, $product_ids)){
      continue;
    }
    $product_id = $order_item->product_id;
    $product = $products[$product_id];
    $supplier_id = $product->supplier_id;
    if($supplier_id == 35){
      $product_sums[$supplier_id][$product_id]['supplier_product_id'] = $products[$product_id]->supplier_product_id;
    }else{
      $product_sums[$supplier_id][$product_id]['supplier_product_id'] = '';
    }
    $product_sums[$supplier_id][$product_id]['name'] = $products[$product_id]->name;
    if($product->type == 'w'){
      $product_sums[$supplier_id][$product_id]['name'] .= ' [ca. '.format_weight($product->kg_per_piece).' kg]';
    }
    $product_sums[$supplier_id][$product_id]['amount_pieces'] += $order_item->amount_pieces;
    $product_sums[$supplier_id][$product_id]['amount_weight'] += $order_item->amount_weight;
    if($product->type == 'k'){
      $product_sums[$supplier_id][$product_id]['amount_needed'] += $order_item->amount_weight;
      $product_sums[$supplier_id][$product_id]['amount_needed_unit'] = 'kg';
    }else{
      $product_sums[$supplier_id][$product_id]['amount_needed'] += $order_item->amount_pieces;
      $product_sums[$supplier_id][$product_id]['amount_needed_unit'] = 'St.';
    }
    $product_sums[$supplier_id][$product_id]['amount_order'] = $product_sums[$supplier_id][$product_id]['amount_needed'];
    $product_sums[$supplier_id][$product_id]['amount_order_unit'] = $product_sums[$supplier_id][$product_id]['amount_needed_unit'];
  }
  foreach($product_sums[35] as $product_id => $oi_sum){
    $product_sums[35][$product_id]['amount_order'] = ceil($oi_sum['amount_needed']/$products[$product_id]->amount_per_bundle);
    $product_sums[35][$product_id]['amount_order_unit'] = 'Gb.';
  }
  foreach($product_sums as $supplier_id => $oi_sums){
    foreach($oi_sums as $product_id => $oi_sum){
      $price = $prices[$product_id]->purchase;
      $product_type = $products[$product_id]->type;
      $product_sums[$supplier_id][$product_id]['price'] = $price;
      if($product_type == 'p'){
        $product_sums[$supplier_id][$product_id]['price_unit'] = 'St.';
      }else{
        $product_sums[$supplier_id][$product_id]['price_unit'] = 'kg';
      }
      $product_sums[$supplier_id][$product_id]['tax'] = $prices[$product_id]->tax;
      if($product_type == 'w'){
        $product_sums[$supplier_id][$product_id]['sum_price'] = round($price * $oi_sum['amount_order'] * $products[$product_id]->kg_per_piece, 2);
      }elseif($oi_sum['amount_order_unit'] == 'Gb.'){
        $product_sums[$supplier_id][$product_id]['sum_price'] = round($price * $oi_sum['amount_order'] * $products[$product_id]->amount_per_bundle, 2);
      }else{
        $product_sums[$supplier_id][$product_id]['sum_price'] = round($price * $oi_sum['amount_order'] ,2);
      }
    }
  }

  ksort($product_sums);

  return $product_sums;
}


function execute_member_orders(){
  if(!user_has_access('orders')){
    forward_to_noaccess();
  }
  #require_once('sql.class.php');
  #$qry = "SELECT pickup_date, count(*) ...
  $pickup_date = '2024-12-20';
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