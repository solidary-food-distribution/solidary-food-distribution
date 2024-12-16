<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  $order_id = get_request_param('order_id');
  $modus = get_request_param('modus');
  $search = get_request_param('search');
  $limit = intval(get_request_param('limit'));
  if($limit == 0){
    $limit = 10;
  }
  $product_id = get_request_param('product_id');
  require_once('orders.class.php');
  $orders = new Orders(array('member_id' => $user['member_id']),array('pickup_date'=>'DESC'));
  if(!$orders->isset($order_id)){
    $order_id = find_current_order_id($orders);
    if($order_id){
      forward_to_page('/order/?order_id='.$order_id);
    }else{
      forward_to_page('/orders');
    }
  }
  $order = $orders->get($order_id);

  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $order_id));

  if($modus == '' && count($order_items)){
    $modus = 'o';
  }elseif($modus == ''){
    forward_to_page('/order/?order_id='.$order_id.'&modus=1');
  }
  require_once('products.class.php');
  require_once('members.class.php');
  if($modus == 'o'){
    $product_ids = $order_items->get_product_ids();
    if(empty($product_ids)){
      $product_ids=array('0');
    }
    $products = new Products(array('id' => $product_ids),array('FIELD(id,'.implode(',',$product_ids).')' => 'ASC'));
    $suppliers = new Members(array('producer>=' => 1));
  }elseif($modus == 'f'){
    require_once('sql.class.php');
    $product_ids=SQL::selectKey2Val("SELECT f.product_id,p.name FROM msl_favorites f,msl_products p WHERE f.product_id=p.id AND member_id='".intval($user['member_id'])."' AND p.status IN ('o','s') ORDER BY p.name",'product_id','product_id');
    if(empty($product_ids)){
      $product_ids=array('0');
    }
    $products = new Products(array('id' => $product_ids),array('FIELD(id,'.implode(',',$product_ids).')' => 'ASC'));
    $suppliers = new Members(array('producer>=' => 1));
  }elseif($modus == '1' || $modus == '2'){
    $suppliers = new Members(array('producer' => $modus));
    $products = new Products(array('supplier_id' => $suppliers->keys(), 'status' => 'o', 'type' => array('k', 'p', 'w')));
    $product_ids = $products->keys();
  }elseif($modus == 's' && trim($search)!=''){
    $suppliers = new Members(array('producer>=' => 1));
    $product_ids = search_products($search, $suppliers, $limit);
    if(empty($product_ids)){
      $product_ids=array('0');
    }
    $products = new Products(array('id' => $product_ids),array('FIELD(id,'.implode(',',$product_ids).')' => 'ASC'));
  }elseif($modus == 's'){
    $products = array();
  }
  if(empty($product_ids)){
    $product_ids=array('0');
  }

  $brands = array();
  if($modus != '1' && !empty($products)){
    require_once('sql.class.php');
    $brands = SQL::selectKey2Val("SELECT id, name FROM msl_brands", 'id', 'name');
  }

  $ois = array();
  $order_items_count = 0;
  foreach($order_items as $oi){
    $ois[$oi->product_id] = $oi;
    if($oi->amount_pieces || $oi->amount_weight){
      $order_items_count++;
    }
  }
  
  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $product_ids, 'start<=' => $order->pickup_date, 'end>=' => $order->pickup_date));
  #logger("prices ".print_r($prices,1));

  require_once('sql.class.php');
  $excl = " AND p.id NOT IN (SELECT product_id FROM (select min(o.pickup_date) minpud,max(o.pickup_date) maxpud,oi.product_id,sum(oi.amount_pieces) summe,p.amount_per_bundle from msl_orders o,msl_order_items oi, msl_products p where o.id=oi.order_id and oi.product_id=p.id and p.supplier_id=35 and oi.amount_pieces>0 and p.status='o' group by oi.product_id,p.amount_per_bundle having minpud='2024-12-06' and summe<amount_per_bundle) excl)";
  $order_sum_oekoring = SQL::selectOne("SELECT SUM((CEIL(oi.amount_pieces/oi.amount_per_bundle)*oi.amount_per_bundle)*(SELECT MIN(purchase) FROM msl_prices pr WHERE pr.product_id=p.id)) order_sum FROM msl_orders o, msl_order_items oi, msl_products p WHERE o.id=oi.order_id AND oi.product_id=p.id AND p.supplier_id=35 AND o.pickup_date='".SQL::escapeString($order->pickup_date)."' $excl")['order_sum'];

  #logger("products ".print_r($products,1));
  $ps = $products;
  $products = array();
  foreach($ps as $p_id => $p){
    if(false && $p->supplier_id == 20 && $order->pickup_date > date('Y-m-d', strtotime('+8 days', time()))){
      //too far in future
    }elseif($prices[$p_id]->price == 0){
      //no price (yet)
    }else{
      $products[$p_id] = $p;
    }
  }

  return array('modus' => $modus, 'order' => $order, 'products' => $products, 'order_items' => $ois, 'order_items_count' => $order_items_count, 'suppliers' => $suppliers, 'prices' => $prices, 'brands' => $brands, 'search' => $search, 'limit' => $limit, 'order_sum_oekoring' => $order_sum_oekoring);
}

function search_products($search, $suppliers, $limit){
  require_once('sql.class.php');
  $qry = "SELECT p.id FROM msl_members m,msl_products p LEFT JOIN msl_brands b ON (brand_id=b.id) WHERE m.id = p.supplier_id AND p.supplier_id IN (".SQL::escapeArray($suppliers->keys()).") AND p.status IN ('o', 's') AND p.type IN ('k', 'p') AND (";
  if(is_numeric($search)){
    $esc_search = SQL::escapeString($search);
    $qry .= "p.supplier_product_id='$esc_search' OR p.gtin_piece='$esc_search' OR p.gtin_bundle='$esc_search'";
  }else{
    $wheres = array();
    $terms = explode(' ',trim($search));
    foreach($terms as $term){
      $term = trim($term);
      if($term == ''){
        continue;
      }
      $esc_term = SQL::escapeString('%'.$term.'%');
      $wheres[] = "(p.name LIKE '$esc_term' OR b.name LIKE '$esc_term')";
    }
    $qry .= implode(' AND ', $wheres);
  }
  $qry .= ") ORDER BY IF(p.status='o', 0, 1), m.producer, p.name, b.name, p.id DESC LIMIT ".intval($limit);
  #logger($qry);
  $res = SQL::selectKey2Val($qry, 'id', 'id');
  return $res;
}

function find_current_order_id($orders){
  if(!$orders->count()){
    return 0;
  }
  $prev_id = 0;
  if($orders->first()->pickup_date > date('Y-m-d')){
    $prev_id = $orders->first()->id;
  }
  foreach($orders->array() as $id => $order){
    if($order->pickup_date <= date('Y-m-d')){
      break;
    }
    $prev_id = $id;
  }
  return $prev_id;
}

function execute_filter_ajax(){
  $order_id = get_request_param('order_id');
  $field = get_request_param('field');
  $value = get_request_param('value');
  set_request_param($field, $value);
  $return = execute_index();
  $return['template'] = 'index.php';
  $return['layout'] = 'layout_null.php';
  return $return;
}


function execute_change_ajax(){
  global $user;
  $order_id = get_request_param('order_id');
  $product_id=intval(get_request_param('product_id'));
  $dir=get_request_param('dir');
  $modus = get_request_param('modus');
  logger("$order_id $product_id $dir");
  if($order_id && $product_id && $dir){
    require_once('order_items.class.php');
    $ois = new OrderItems(array('order_id' => $order_id, 'product_id' => $product_id));
    if(!$ois->count()){
      $oi = OrderItem::create($order_id, $product_id);
    }else{
      $oi = $ois->first();
    }
    require_once('products.class.php');
    $product = Products::sget($product_id);
    if($product->type == 'p' || $product->type == 'w'){
      $amount = $oi->amount_pieces;
      $amount_field = 'amount_pieces';
    }elseif($product->type == 'k'){
      $amount = $oi->amount_weight;
      $amount_field = 'amount_weight';
    }else{
      throw new Exception("unknown product-type ".print_r($product,1), 1);
      exit;
    }
    $change = ($dir>0)?1:-1;
    if($product->status == 'o'){
      $change *= $product->amount_steps;
    }elseif($product->status == 's'){
      $change *= $product->amount_per_bundle;
    }

    $amount_new = round($amount + $change, 3);
    if($amount_new < $product->amount_min && $dir<0){
      $amount_new = 0;
    }elseif($amount_new < $product->amount_min && $dir>0){
      $amount_new = $product->amount_min;
    }elseif($amount_new > $product->amount_max){
      $amount_new = $product->amount_max;
    }
    if($modus == 'o' && $amount == 0 && $dir < 0){
      $oi->delete();
    }elseif($modus !='o' && $amount_new == 0){
      $oi->delete();
    }else{
      $updates = array($amount_field => $amount_new);
      if($product->type == 'w'){
        $updates['amount_weight'] = $amount_new * $product->kg_per_piece;
      }
      $oi->update($updates);
      update_order_item_prices($oi->id);
    }
  }
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}

function update_order_item_prices($order_item_id){
  require_once('order_items.class.php');
  $order_item = OrderItems::sget($order_item_id);
  require_once('products.class.php');
  $product = Products::sget($order_item->product_id);
  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $product->id, 'start<=' => date('Y-m-d'), 'end>=' => date('Y-m-d')));
  $dbprice = $prices[$product->id];
  $updates = array();
  if($product->type == 'p'){
    $price_type = 'p';
  }else{
    $price_type = 'k';
  }
  if($order_item->price_type != $price_type){
    $updates['price_type'] = $price_type;
  }
  if($order_item->price != $dbprice->price){
    $updates['price'] = $dbprice->price;
  }
  if($order_item->amount_per_bundle != $dbprice->amount_per_bundle){
    $updates['amount_per_bundle'] = $dbprice->amount_per_bundle;
  }
  if($order_item->price_bundle != $dbprice->price_bundle){
    $updates['price_bundle'] = $dbprice->price_bundle;
  }
  if($price_type == 'p'){
    if($dbprice->amount_per_bundle > 1 && $order_item->amount_pieces >= $dbprice->amount_per_bundle){
      $price_sum = round($order_item->amount_pieces * $dbprice->price_bundle, 2);
    }else{
      $price_sum = round($order_item->amount_pieces * $dbprice->price, 2);
    }
  }else{
    $price_sum = round($order_item->amount_weight * $dbprice->price, 2);
  }
  if($order_item->price_sum != $price_sum){
    $updates['price_sum'] = $price_sum;
  }
  if(!empty($updates)){
    $order_item->update($updates);
  }
}