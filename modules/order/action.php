<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  if(!isset($user['access']['order'][$user['member_id']]) || $user['access']['order'][$user['member_id']]['end']<date('Y-m-d')){
    forward_to_noaccess();
  }
  $order_id = get_request_param('order_id');
  $modus = get_request_param('modus');
  $search = get_request_param('search');
  $scategories = get_request_param('categories');
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
      forward_to_page('/order/?order_id='.$order_id.'&modus='.$modus.'&search='.urlencode($search));
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

  if(trim($scategories) != ''){
    $scategories = explode('|', $scategories);
    $scategories = array_flip($scategories);
  }else{
    $scategories = array();
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
    $products = new Products(array('supplier_id' => $suppliers->keys(), 'status' => array('o','e'), 'type' => array('k', 'p', 'w')));
    $product_ids = $products->keys();
  }elseif($modus == 's' && (trim($search) != '' || !empty($scategories))){
    $suppliers = new Members(array('producer>=' => 1));
    $product_ids = search_products($search, $scategories, $suppliers);
    if(empty($product_ids)){
      $product_ids=array('0');
    }
    $products = new Products(array('id' => $product_ids),array('FIELD(id,'.implode(',',$product_ids).')' => 'ASC'));
  }elseif($modus == 's'){
    $suppliers = new Members(array('producer>=' => 1));
    $products = new Products(array('supplier_id' => $suppliers->keys(), 'status' => array('o', 's')));
  }
  $categories = array();
  if($modus == 's'){
    foreach($products as $product){
      $categories[$product->category] += 1;
    }
    if(isset($categories[''])){
      $categories['-'] = $categories[''];
      unset($categories['']);
    }
    if(trim($search) == '' && empty($scategories)){
      $products = array();
    }
  }

  require_once('sql.class.php');
  $favorites = SQL::selectKey2Val("SELECT product_id, 1 AS value FROM msl_favorites WHERE member_id=".intval($user['member_id']), 'product_id', 'value');

  $brands = array();
  if(!empty($products)){
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

  $supplier_unlocked = get_supplier_unlocked($order->id);
  $order_sum_oekoring = -1;
  if(isset($supplier_unlocked[35])){
    $order_sum_oekoring = get_oekoring_order_sum($order->pickup_date);
  }
  return array('modus' => $modus, 'order' => $order, 'products' => $products, 'favorites' => $favorites, 'order_items' => $ois, 'order_items_count' => $order_items_count, 'suppliers' => $suppliers, 'supplier_unlocked' => $supplier_unlocked, 'prices' => $prices, 'brands' => $brands, 'search' => $search, 'limit' => $limit, 'categories' => $categories, 'scategories' => $scategories, 'order_sum_oekoring' => $order_sum_oekoring);
}

function get_supplier_unlocked($order_id){
  require_once('orders.class.php');
  $order = Orders::sget($order_id);
  require_once('delivery_dates.class.php');
  $delivery_dates = new DeliveryDates(array('date' => $order->pickup_date));
  require_once('purchases.class.php');
  $purchases = new Purchases(array('delivery_date_id' => $delivery_dates->keys(), 'status' => array('a')));
  $supplier_unlocked = array();
  foreach($purchases as $purchase){
    if($purchase->datetime > date('Y-m-d H:i:s')){
      $supplier_unlocked[$purchase->supplier_id] = $purchase->datetime;
    }
  }
  return $supplier_unlocked;
}

function search_products($search, $scategories, $suppliers){
  require_once('sql.class.php');
  $qry = "SELECT p.id FROM msl_members m,msl_products p LEFT JOIN msl_brands b ON (brand_id=b.id) WHERE m.id = p.supplier_id AND p.supplier_id IN (".SQL::escapeArray($suppliers->keys()).") AND p.status IN ('o', 's') AND p.type IN ('k', 'p', 'w') AND (";
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
  if(!empty($scategories)){
    $wheres = array();
    if(isset($scategories['-'])){
      unset($scategories['-']);
      $scategories['']=1;
    }
    foreach($scategories as $scategory => $dummy){
      $wheres[] = "p.category = '".SQL::escapeString($scategory)."'";
    }
    if($search!=''){
      $qry .= ' AND ';
    }
    $qry .= implode(' AND ', $wheres);
  }
  $qry .= ") ORDER BY IF(p.status='o', 0, 1), m.producer, p.name, b.name, p.id DESC";
  #logger($qry);
  $res = SQL::selectKey2Val($qry, 'id', 'id');
  return $res;
}

function execute_infos_lazy_load_ajax(){
  $product_ids = get_request_param('product_ids');
  $product_ids = explode(',', $product_ids);
  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));
  $gtins = array();
  foreach($products as $product){
    if($product->supplier_id == 35 && trim($product->gtin_piece) != ''){
      $gtins[$product->gtin_piece] = $product->id;
    }
  }
  $infos = array();
  if(!empty($gtins)){
    //1. ecoinform
    $url = 'https://db.ecoinform.de/web/ecodb.php/produktliste/json?partner=674a474e75a77642&eanliste='.implode(',',array_keys($gtins));
    $ecodata = file_get_contents($url);
    if(!empty($ecodata)){
      $ecodata = json_decode($ecodata, true);
      foreach($ecodata['produkte'] as $eco_id => $data){
        if(isset($gtins[$data['p_ean1']])){
          $product_id = $gtins[$data['p_ean1']];
          $pinfos = array(
            'date' => date('Y-m-d'),
            'link' => 'https://www.ecoinform.de/main.php/detail?id='.$eco_id,
          );
          if(!empty($data['p_bild'])){
            $pinfos['image'] = 'https://img.ecoinform.de'.$data['p_bild'];
          }
          $json = json_encode($pinfos);
          $products[$product_id]->update(array('infos' => $json));
          $products[$product_id]->infos = $json;
          $infos[$product_id] = $pinfos;
        }
      }
      //$datanature="712*j<tOH7RZ%/V";
    }
    //2. duckduckgo search
    $add_search = array();
    $brand_ids = array();
    foreach($gtins as $gtin => $product_id){
      if(!isset($infos[$product_id])){
        $add_search[$product_id] = 1;
        $brand_ids[$products[$product_id]->brand_id] = 1;
      }
    }
    if(!empty($brand_ids)){
      require_once('sql.class.php');
      $brands = SQL::selectKey2Val("SELECT id, name FROM msl_brands WHERE id IN (".SQL::escapeArray(array_keys($brand_ids)).")", 'id', 'name');
    }
    foreach($add_search as $product_id => $dummy){
      $product = $products[$product_id];
      $pinfos = array(
        'date' => date('Y-m-d'),
        'link' => 'https://duckduckgo.com/?q='.$product->gtin_piece.'+'.urlencode(trim($brands[$product->brand_id].' '.$product->name)),
        'image' => '/img/search.png',
      );
      $json = json_encode($pinfos);
      $products[$product_id]->update(array('infos' => $json));
      $products[$product_id]->infos = $json;
      $infos[$product_id] = $pinfos;
    }
  }
  echo json_encode($infos);
  exit;
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
  $notify = '';
  #logger("$order_id $product_id $dir");
  if($order_id && $product_id && $dir){
    $supplier_unlocked = get_supplier_unlocked($order_id);
    require_once('products.class.php');
    $product = Products::sget($product_id);
    if(isset($supplier_unlocked[$product->supplier_id])){
      require_once('order_items.class.php');
      $ois = new OrderItems(array('order_id' => $order_id, 'product_id' => $product_id));
      if(!$ois->count()){
        $oi = OrderItem::create($order_id, $product_id);
      }else{
        $oi = $ois->first();
      }
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
        if($dir > 0 && $_SESSION['member']['order_limit'] && get_order_sum($order_id) > $_SESSION['member']['order_limit']){
          $updates = array($amount_field => $amount);
          if($product->type == 'w'){
            $updates['amount_weight'] = $amount * $product->kg_per_piece;
          }
          $oi->update($updates);
          update_order_item_prices($oi->id);
          $notify = "Bestellgrenze von ".$_SESSION['member']['order_limit']." EUR erreicht.";
        }
      }
      require_once('inventory.inc.php');
      update_inventory_product($product_id);
    }
  }
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  $return['notify'] = $notify;
  return $return;
}

function get_order_sum($order_id){
  $ois = new OrderItems(array('order_id' => $order_id));
  $order_sum = 0;
  foreach($ois as $oi){
    $order_sum += $oi->price_sum;
  }
  return $order_sum;
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

function get_oekoring_order_sum($pickup_date){
  require_once('purchases.inc.php');
  $order_sum = purchases_get_sum($pickup_date, 35);
  return $order_sum;
}

function execute_favorite(){
  global $user;
  $product_id = get_request_param('product_id');
  $set = get_request_param('set');
  require_once('sql.class.php');
  if(intval($set)){
    SQL::update("INSERT INTO msl_favorites (member_id, product_id, created) VALUES (".intval($user['member_id']).",".intval($product_id).",NOW())");
  }else{
    SQL::update("DELETE FROM msl_favorites WHERE member_id=".intval($user['member_id'])." AND product_id=".intval($product_id));
  }
  echo json_encode(array('set' => $set));
  exit;
}