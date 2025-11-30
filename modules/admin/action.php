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
  $infos = user_has_access('infos');
  $polls = user_has_access('polls');
  $thaler = user_has_access('thaler');
  if(!$products && !$members && !$users && !$orders && !$purchases && !$debits && !$remote && !$mails && !$infos && !$polls && !$thaler){
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
    'infos' => $infos,
    'polls' => $polls,
    'thaler' => $thaler,
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

  $product_id = get_request_param('product_id');

  require_once('products.class.php');
  $categories = array();
  $products = new Products(array('status' => array('o', 's')));
  foreach($products as $product){
    $categories[$product->category] += 1;
  }
  ksort($categories);

  $filter = array('supplier_id' => $supplier_id);
  if($status !== ''){
    $filter['status'] = $status;
  }
  if($product_id){
    $filter['id'] = $product_id;
  }
  $products = new Products($filter);

  require_once('sql.class.php');
  $brands = SQL::selectKey2Val("SELECT id, name FROM msl_brands WHERE supplier_id='".intval($supplier_id)."' ORDER BY name", 'id', 'name');

  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $products->keys()));

  return array('supplier' => $supplier, 'products' => $products, 'prices' => $prices, 'categories' => $categories, 'status' => $status, 'brands' => $brands);
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

  if(strpos(',purchase,price,tax', $field)){
    $value = str_replace(',', '.', $value);
    require_once('prices.class.php');
    $prices = new Prices(array('product_id' => $product_id, 'start<=' => date('Y-m-d'), 'end>=' => date('Y-m-d')));
    foreach($prices as $price){
      $price->update(array($field => $value));
    }
  }else{
    require_once('products.class.php');
    $product = Products::sget($product_id);
    $updates = array($field => $value);
    if($field == 'name' && $supplier_id != 35){
      $updates['supplier_product_id'] = $value;
    }
    $product->update($updates);
  }
  echo json_encode(array('result' => '1'));
  exit();
}

function execute_product_new(){
  $supplier_id = get_request_param('supplier_id');
  require_once('product.class.php');
  $product = Product::create(array('supplier_id' => $supplier_id, 'status' => 'n'));

  require_once('prices.class.php');
  $price = Price::create($product->id);

  forward_to_page('/admin/products', 'supplier_id='.$supplier_id.'&product_id='.$product->id);
}

function execute_products_suppliers(){
  require_once('members.class.php');
  $suppliers = new Members(array('producer>' => '0'));
  return array('suppliers' => $suppliers);
}


function execute_products_import_friedls(){
  global $user;

  $status = get_request_param('status');

  $rows = array();
  
  #logger(print_r($_FILES,1));
  require_once('sql.class.php');
  if(!empty($_FILES['file']['name']) && $_FILES['file']['size']>0 && $_FILES['file']['error']==0){
    
    $upload_id = SQL::insert("INSERT INTO msl_uploads (filename, user_id) VALUES ('".SQL::escapeString(basename($_FILES['file']['name']))."','".$user['user_id']."')");
    $target =  $_SERVER['DOCUMENT_ROOT'].'/../data/uploads/'.$upload_id;
    move_uploaded_file($_FILES['file']['tmp_name'], $target);
    require_once('SimpleXLSX.php');
    $xlsx = Shuchkin\SimpleXLSX::parse($target);
    $started = 0;
    foreach($xlsx->rows() as $row_nr => $row){
      if($row[0] == 'Bestellen'){
        $started = 1;
      }elseif($started){
        if($row[0] == 'Pause' || $row[0] == 'aus' || $row[1] == '' || $row[3] == '' || $row[1] == 'zzgl. Pfand/Kiste inkl. Flaschen'){
          continue;
        }
        SQL::insert("INSERT INTO msl_product_imports (upload_id, row_nr, info, name, type, purchase, brand) VALUES ('$upload_id', '$row_nr', '".SQL::escapeString(trim($row[0]))."', '".SQL::escapeString(trim($row[1]))."', '".SQL::escapeString(trim($row[2]))."', '".SQL::escapeString(trim($row[3]))."', '".SQL::escapeString(trim($row[4]))."')");
      }
    }
  }
  $uploads = SQL::selectId("SELECT *,(SELECT COUNT(*) FROM msl_product_imports WHERE upload_id=u.id AND status='') open, (SELECT COUNT(*) FROM msl_product_imports WHERE upload_id=u.id AND status='1') ok FROM msl_uploads u ORDER BY 1 DESC LIMIT 3", 'id');

  if(!isset($upload_id)){
    $upload_id = intval(get_request_param('upload_id'));
  }
  if(!isset($upload_id)){
    $upload_id = key($uploads);
  }

  $rows = SQL::selectId("SELECT * FROM msl_product_imports WHERE upload_id='".intval($upload_id)."' AND status='".SQL::escapeString($status)."' ORDER BY 1", 'id');
  
  require_once('products.class.php');
  $products = new Products(array('supplier_id' => 20, 'status!=' => 'd'));

  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $products->keys()));

  $products_search = array();
  foreach($products as $product){
    $products_search[$product->supplier_name] = $product->id;
  }

  foreach($rows as $row_id => &$row){
    if($row['product_id']==-1){
      continue;
    }elseif($row['product_id']==0){
      $search = trim(trim($row['name']).' '.trim($row['brand']));
      if(isset($products_search[$search])){
        $row['product_id'] = $products_search[$search];
        SQL::update("UPDATE msl_product_imports SET product_id='".intval($row['product_id'])."' WHERE id='".intval($row['id'])."'");
      }
    }
  }

  $brands = SQL::selectKey2Val("SELECT id, name FROM msl_brands WHERE supplier_id=20 ORDER BY name", 'id', 'name');

  require_once('members.class.php');
  $supplier = Members::sget(20);

  return array('uploads' => $uploads, 'supplier' => $supplier, 'products' => $products, 'prices' => $prices, 'brands' => $brands, 'rows' => $rows);
}

function execute_products_import_friedls_update_ajax(){
  $row_id = get_request_param('row_id');
  $field = get_request_param('field');
  $value = get_request_param('value');
  require_once('products.class.php');
  require_once('sql.class.php');
  $row = SQL::selectOne("SELECT * FROM msl_product_imports WHERE id='".intval($row_id)."'");
  if($field == 'ok'){
    //if it is the first product to be set ok, we deactivate all products from the supplier
    $count=SQL::selectOne("SELECT COUNT(*) AS cnt FROM msl_product_imports WHERE status='1' AND upload_id=(SELECT upload_id FROM msl_product_imports WHERE id='".intval($row_id)."')")['cnt'];
    if($count==0){
      SQL::update("UPDATE msl_products SET status='n' WHERE status='o' AND supplier_id=20");
    }
    products_import_friedls_update_field($row, 'status', 'o');
    products_import_friedls_update_field($row, 'purchase', $row['purchase']);
    SQL::update("UPDATE msl_product_imports SET status='1' WHERE id='".intval($row_id)."'");
  }else{
    products_import_friedls_update_field($row, $field, $value);
  }
  logger("row_id:$row_id\nfield:$field\nvalue:$value");
  set_request_param('upload_id', $row['upload_id']);
  $return = execute_products_import_friedls();
  $return['template'] = 'products_import_friedls.php';
  $return['layout'] = 'layout_null.php';
  return $return;
}
function products_import_friedls_update_field($row,$field,$value){
  if($field == 'product_id' && trim($value)!==''){
    if(intval($value)){
      $search = trim(trim($row['name']).' '.trim($row['brand']));
      $products = new Products(array('supplier_name' => $search));
      foreach($products as $product){
        $product->update(array('supplier_name' => '')); //reset/unlink
      }
      $product = Products::sget(intval($value));
      $product->update(array('supplier_name' => $search));
      SQL::update("UPDATE msl_product_imports SET product_id='".intval($product->id)."' WHERE id='".intval($row['id'])."'");
    }else{
      SQL::update("UPDATE msl_product_imports SET product_id=-1 WHERE id='".intval($row['id'])."'");
    }
  }elseif($field == 'new_product_name'){
    $supplier_name = trim(trim($row['name']).' '.trim($row['brand']));
    $product = Product::create(array('supplier_id' => 20, 'status' => 'n', 'supplier_name' => $supplier_name, 'supplier_product_id' => $supplier_name, 'name' => trim($value)));
    require_once('prices.class.php');
    $price = Price::create($product->id);
    $price->update(array('purchase' => $row['purchase'], 'tax' => 7));
    SQL::update("UPDATE msl_product_imports SET product_id='".intval($product->id)."' WHERE id='".intval($row['id'])."'");
  }elseif($row['product_id']>0 && strpos(',purchase,tax,price', $field)){
    require_once('prices.class.php');
    $prices = new Prices(array('product_id' => intval($row['product_id'])));
    if($prices->count()){
      $price = $prices->first();
    }else{
      $price = Price::create($row['product_id']);
      $price->update(array('purchase' => $row['purchase'], 'tax' => 7));
    }
    $value = str_replace(',', '.', $value);
    logger($price->id." prices ".$field." ".$value);
    $price->update(array($field => $value));
  }elseif($row['product_id']>0){
    $product = Products::sget(intval($row['product_id']));
    if(strpos(',kg_per_piece,amount_steps', $field)){
      $value = str_replace(',', '.', $value);
    }
    if($field!='kg_per_piece' && $product->type == 'w'){
      $kg_per_piece = get_product_kg_per_piece_by_pickups($product->id);
      if($kg_per_piece){
        logger($product->id." kg_per_piece $kg_per_piece");
        $product->update(array('kg_per_piece' => $kg_per_piece));
      }
    }
    logger($product->id." ".$field." ".$value);
    $product->update(array($field => $value));
  }
}

function get_product_kg_per_piece_by_pickups($product_id){
  $kg_per_piece = 0;
  $avgs = array();
  $cnts = 0;
  $min_date = '';
  require_once('sql.class.php');
  $qry = "SELECT pickup_date,AVG(avg) AS avg,COUNT(*) AS cnt FROM (SELECT o.pickup_date, pui.amount_weight / pui.amount_pieces AS avg FROM msl_pickup_items pui, msl_order_items oi, msl_orders o WHERE o.id=oi.order_id AND oi.id=pui.order_item_id AND pui.product_id='".intval($product_id)."' AND pui.amount_pieces>0 AND pui.amount_weight>0) t1 GROUP BY pickup_date HAVING cnt>1 ORDER BY pickup_date DESC LIMIT 10";
  $res = SQL::select($qry);
  foreach($res as $v){
    if(empty($min_date)){
      $min_date = date('Y-m-d', strtotime('-4 WEEKS', strtotime($v['pickup_date'])));
    }
    if($v['pickup_date'] < $min_date){
      break;
    }
    $avgs[] = $v['avg'];
    $cnts += $v['cnt'];
    if($cnts > 5){
      break;
    }
  }
  if(count($avgs)){
    $kg_per_piece = array_sum($avgs) / count($avgs);
    if($kg_per_piece < 0.25 ){
      $kg_per_piece = 10 * round($kg_per_piece/10, 3);
    }elseif($kg_per_piece < 0.5 ){
      $kg_per_piece = 25 * round($kg_per_piece/25, 3);
    }elseif($kg_per_piece < 1 ){
      $kg_per_piece = 50 * round($kg_per_piece/50, 3);
    }else{
      $kg_per_piece = 100 * round($kg_per_piece/100, 3);
    }
  }
  return $kg_per_piece;
}


function execute_purchases(){
  if(!user_has_access('purchases')){
    forward_to_noaccess();
  }

  $dates = get_delivery_dates();
  $date = $dates['date'];
  $date_prev = $dates['date_prev'];
  $date_next = $dates['date_next'];

  require_once('delivery_dates.class.php');
  $delivery_dates = new DeliveryDates(array('date' => $date));
  $delivery_date = $delivery_dates->first();

  require_once('purchases.class.php');
  $purchases = new Purchases(array('delivery_date_id' => $delivery_date->id));
  $member_ids = array();
  $delivery_date_ids = array();
  foreach($purchases as $purchase){
    $member_ids[$purchase->supplier_id] = 1;
    $delivery_date_ids[$purchase->delivery_date_id] = 1;
  }

  require_once('members.class.php');
  $suppliers = new Members(array('id' => array_keys($member_ids)));

  return array('date' => $date, 'date_prev' => $date_prev, 'date_next' => $date_next, 'purchases' => $purchases, 'delivery_dates' => $delivery_dates, 'suppliers' => $suppliers);
}

function execute_purchase_date_ajax(){
  if(!user_has_access('purchases')){
    forward_to_noaccess();
  }
  $purchase_id = get_request_param('purchase_id');
  $field = get_request_param('field');
  $value = get_request_param('value');

  require_once('purchases.class.php');
  $purchase = Purchases::sget($purchase_id);

  if($field != ''){
    $updates = array();
    $date = substr($purchase->datetime,0,10);
    $time = substr($purchase->datetime,11,8);
    if($field == 'date'){
      $date = date('Y-m-d',strtotime(($value == 'prev'?'-':'+').'1 DAYS',strtotime($date)));
    }elseif($field == 'time'){
      $time = date('H:i:s',strtotime(($value == 'prev'?'-':'+').'1 HOURS',strtotime($time)));
    }
    $datetime = $date.' '.$time;
    if($datetime != $purchase->datetime){
      $updates['datetime'] = $datetime;
    }
    if(!empty($updates)){
      $purchase->update($updates);
      $purchase = Purchases::sget($purchase_id);
    }
  }

  require_once('delivery_dates.class.php');
  $delivery_date = DeliveryDates::sget($purchase->delivery_date_id);

  require_once('members.class.php');
  $supplier = Members::sget($purchase->supplier_id);

  return array('purchase' => $purchase, 'delivery_date' => $delivery_date, 'supplier' => $supplier, 'layout' => 'layout_null.php');
}

function execute_purchase_update_ajax(){
  if(!user_has_access('purchases')){
    forward_to_noaccess();
  }
  $purchase_id = get_request_param('purchase_id');
  $date = get_request_param('date');
  $field = get_request_param('field');
  $value = get_request_param('value');

  if($field == 'status'){
    require_once('purchases.class.php');
    $purchase = Purchases::sget($purchase_id);
    $purchase->update(array('status' => $value));
  }
  echo json_encode(array('result' => '1', 'location_href' => '/admin/purchases?date='.$date));
  exit();
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
  #logger("product_sums ".print_r($product_sums,1));

  require_once('products.class.php');
  $products = new Products(array('id' => array_keys($product_sums)));

  require_once('members.class.php');
  $supplier = Members::sget($purchase->supplier_id);

  return array('purchase' => $purchase, 'delivery_date' => $delivery_date, 'product_sums' => $product_sums, 'products' => $products, 'supplier' => $supplier);
}

function get_delivery_dates(){
  $date = get_request_param('date');
  if($date == ''){
    $date = date('Y-m-d');
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

  return array('date' => $date, 'date_prev' => $date_prev, 'date_next' => $date_next);
}

function execute_orders(){
  if(!user_has_access('orders')){
    forward_to_noaccess();
  }

  $dates = get_delivery_dates();
  $date = $dates['date'];
  $date_prev = $dates['date_prev'];
  $date_next = $dates['date_next'];
  #logger("date $date_prev $date $date_next");

  $orders_next = $date_next;
  if(!$orders_next){
    $orders_next=date('Y-m-d',strtotime('+1 DAYS',strtotime($date)));
  }

  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date>=' => $date, 'pickup_date<' => $orders_next));
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $orders->keys()));

  require_once('pickups.class.php');
  $pickups = new Pickups(array('created>' => $date, 'created<' => $orders_next));
  require_once('pickup_items.class.php');
  $pickup_items = new PickupItems(array('pickup_id' => $pickups->keys()));
  $pui_product_ids = $pickup_items->get_product_ids();

  $product_ids = $order_items->get_product_ids();
  if(!empty($pui_product_ids)){
    $product_ids += $pui_product_ids;
  }
  require_once('products.class.php');
  $products = new Products(array('supplier_id' => 20, 'status!=' => 'd'));
  $product_ids += $products->keys();
  $products = new Products(array('id' => $product_ids));

  $supplier_ids = array();
  $member_orders = array();
  $order_items_array = array();
  $replaced_items = array();
  foreach($order_items as $order_item){
    if($order_item->amount_pieces == 0 && $order_item->amount_weight == 0){
      continue;
    }
    $order = $orders[$order_item->order_id];
    $member_orders[$order->member_id][$order->id] = $order;
    $product_id = $order_item->product_id;
    $supplier_id = $products[$product_id]->supplier_id;
    $supplier_ids[$supplier_id] = 1;
    $order_items_array[$order->id][$supplier_id.' '.$products[$product_id]->name.' '.$product_id.' '.$order_item->id] = $order_item;
    if($order_item->replaces_id){
      $replaced_items[$order_item->replaces_id][]=$order_item_id;
    }
  }

  $pickup_items_array = array();
  foreach($pickup_items as $pickup_item){
    $id = $pickup_item->order_item_id;
    if(!$id){
      $id = 'noi'.$pickup_item->id;
    }
    $member_id = $pickups[$pickup_item->pickup_id]->member_id;
    $pickup_items_array[$member_id][$id] = $pickup_item;
  }

  require_once('members.class.php');
  $suppliers = new Members(array('id' => array_keys($supplier_ids)));
  $members = new Members(array('id' => array_keys($member_orders)));

  return array('date' => $date, 'date_prev' => $date_prev, 'date_next' => $date_next, 'member_orders' => $member_orders, 'members' => $members, 'order_items_array' => $order_items_array, 'replaced_items' => $replaced_items, 'pickup_items_array' => $pickup_items_array, 'products' => $products, 'suppliers' => $suppliers);
}

function execute_orders_update_ajax(){
  if(!user_has_access('orders')){
    forward_to_noaccess();
  }
  $order_item_id = get_request_param('order_item_id');
  $pickup_item_id = get_request_param('pickup_item_id');
  $product_id = get_request_param('product_id');
  $amount_order = get_request_param('amount_order');
  $amount_pickup = get_request_param('amount_pickup');
  logger("$order_item_id $pickup_item_id $product_id $amount_order $amount_pickup");
  if($pickup_item_id){
    require_once('pickup_items.class.php');
    $pui = PickupItems::sget($pickup_item_id);
    require_once('products.class.php');
    $product = Products::sget($product_id);
    if($product->type == 'k'){
      $amount_field = 'amount_weight';
      $amount = $pui->amount_weight;
    }elseif($product->type == 'p'){
      $amount_field = 'amount_pieces';
      $amount = $pui->amount_pieces;
    }else{
      logger("product->type not implemented ".$product->type);
      exit;
    }
    if($pui->product_id != $product_id){
      logger("product->id update not implemented");
      exit;
    }
    if(round($amount,3) != round(floatval(str_replace(',', '.', $amount_pickup)),3)){
      $pui->update(array($amount_field => $amount_pickup));
      require_once('pickups.inc.php');
      update_pickup_item_price_sum($pui->id);
    }
  }
  exit;
}

function execute_pickup_emails(){
  $data = execute_orders();
  $date = $data['date'];

  $users = array();
  $members = $data['members'];
  if($members->count()){
    $pickup_items_array = $data['pickup_items_array'];
    $member_ids = array();
    foreach($members as $member_id => $member){
      if(!isset($pickup_items_array[$member_id])){
        $member_ids[] = $member_id;
      }
    }
    require_once('users.class.php');
    $users = new Users(array('member_id' => $member_ids));
  }
  return array('date' => $date, 'users' => $users);
}

function execute_infos(){
  if(!user_has_access('infos')){
    forward_to_noaccess();
  }
  require_once('infos.class.php');
  $infos = new Infos();
  return array('infos' => $infos);
}

function execute_info_new(){
  if(!user_has_access('infos')){
    forward_to_noaccess();
  }
  require_once('info.class.php');
  $info = Info::create();
  forward_to_page('/admin/info?info_id='.$info->id);
}

function execute_info(){
  if(!user_has_access('infos')){
    forward_to_noaccess();
  }
  $info_id = get_request_param('info_id');
  require_once('infos.class.php');
  $info = Infos::sget($info_id);
  if(empty($info)){
    http_response_code(404);
    die('This info was not found');
  }
  return array('info' => $info);
}

function execute_info_ajax(){
  if(!user_has_access('infos')){
    forward_to_noaccess();
  }
  $info_id = get_request_param('info_id');
  $field = get_request_param('field');
  $type = get_request_param('type');
  $value = get_request_param('value');
  if($field == 'status'){
    $field = 'published';
    if($value == '0'){
      $value = '0000-00-00 00:00:00';
    }else{
      $value = date('Y-m-d H:i:s');
    }
  }
  require_once('infos.class.php');
  $info = Infos::sget($info_id);
  $info->update(array($field => $value));
  echo json_encode(array('value' => $value));
  exit;
}

function execute_polls(){
  if(!user_has_access('polls')){
    forward_to_noaccess();
  }
  require_once('polls.class.php');
  $polls = new Polls();
  return array('polls' => $polls);
}

function execute_poll(){
  if(!user_has_access('infos')){
    forward_to_noaccess();
  }
  $poll_id = get_request_param('poll_id');
  require_once('polls.class.php');
  $poll = poll_get($poll_id);
  if(!$poll){
    exit;
  }
  require_once('poll_answers.class.php');
  $poll_answers = new PollAnswers(array('poll_id' => $poll_id), array('ordering' => 'ASC', 'answer' => 'ASC'));
  if(($poll->type == 'm' || $poll->type == 'r') && $poll->has_votes && count($poll_answers)){
    require_once('poll_votes.class.php');
    $poll_votes = new PollVotes(array('poll_answer_id' => $poll_answers->keys(), 'value' => '1'));
    $votes = array();
    $user_ids = array();
    foreach($poll_votes as $poll_vote){
      $votes[$poll_vote->poll_answer_id][] = $poll_vote;
      $user_ids[] = $poll_vote->user_id;
    }
  }
  require_once('sql.class.php');
  $qry = "SELECT GROUP_CONCAT(m.id) member_ids FROM msl_members m WHERE m.id NOT IN (SELECT u.member_id FROM msl_users u, msl_poll_answers pa, msl_poll_votes pv WHERE pa.poll_answer_id=pv.poll_answer_id AND pv.value=1 AND pv.user_id=u.id AND pa.poll_id=".$poll->poll_id.") AND m.id NOT IN (1) AND m.consumer=1";
  $member_ids = SQL::selectOne($qry)['member_ids'];
  $member_ids = explode(',', $member_ids);
  require_once('members.class.php');
  $missing_members = new Members(array('id' => $member_ids));
  require_once('users.class.php');
  $users = new Users();
  return array('poll' => $poll, 'poll_answers' => $poll_answers, 'votes' => $votes, 'users' => $users, 'missing_members' => $missing_members);
}

function execute_poll_update_ajax(){
  if(!user_has_access('infos')){
    forward_to_noaccess();
  }
  global $user;
  $poll_id = get_request_param('poll_id');
  $member_id = get_request_param('member_id');
  require_once('users.class.php');
  $users = new Users(array('member_id' => $member_id), array('id' => 'ASC'));
  $pvuser = $users->first();
  $user_id = $pvuser->id;
  $poll_answer_id = get_request_param('poll_answer_id');
  require_once('poll_votes.class.php');
  $poll_votes = new PollVotes(array('poll_answer_id' => $poll_answer_id, 'user_id' => $user_id));
  if(!count($poll_votes)){
    PollVotes::create($poll_answer_id, $user_id, '1', $user['user_id']);
  }else{
    $poll_votes->first()->update(array('value' => '1'));
  }
  exit;
}