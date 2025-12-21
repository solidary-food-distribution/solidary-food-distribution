<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('inventory');

require_once('inventory.inc.php');

function execute_index(){
  $modus = get_request_param('modus');
  if(empty($modus)){
    $modus = '2';
  }
  $search = trim(get_request_param('search'));
  $limit = intval(get_request_param('limit'));
  if($limit == 0){
    $limit = 20;
  }

  update_inventory();
  $data = get_inventory();

  if($modus == 's' && trim($search) == ''){
    $products = array();
  }elseif($modus == 's'){
    require_once('members.class.php');
    $suppliers = new Members(array('producer' => 2));
    $product_ids = search_products($search, $suppliers, $limit);
    if(empty($product_ids)){
      $product_ids=array('0');
    }
    require_once('products.class.php');
    $products = new Products(array('id' => $product_ids),array('FIELD(id,'.implode(',',$product_ids).')' => 'ASC'));
  }elseif($modus=='2'){
    $product_ids = array();
    require_once('products.class.php');
    $products = new Products(array('stock' => array('o', 'i'), 'status' => 'o'), array('FIELD(type,\'k\',\'w\',\'p\')' => '', 'name' => 'ASC'));
    $supplier_ids = array();
    foreach($products as $product){
      $supplier_ids[$product->supplier_id] = $product->supplier_id;
    }
    require_once('members.class.php');
    $suppliers = new Members(array('id' => $supplier_ids));
  }

  if(!isset($suppliers)){
    require_once('members.class.php');
    $suppliers = new Members(array('producer>=' => 1));
  }

  require_once('sql.inc.php');
  $brands = sql_select_key2value("SELECT id, name FROM msl_brands", 'id', 'name');

  return array(
    'data' => $data,
    'products' => $products,
    'modus' => $modus,
    'suppliers' => $suppliers,
    'brands' => $brands,
    'search' => $search,
  );
}

function execute_filter_ajax(){
  $pickup_id = get_request_param('pickup_id');
  $field = get_request_param('field');
  $value = get_request_param('value');
  set_request_param($field, $value);
  $return = execute_index();
  $return['template'] = 'index.php';
  $return['layout'] = 'layout_null.php';
  return $return;
}

function search_products($search, $suppliers, $limit){
  require_once('sql.inc.php');
  $qry = "SELECT p.id FROM msl_members m,msl_products p LEFT JOIN msl_brands b ON (brand_id=b.id) WHERE m.id = p.supplier_id AND p.supplier_id IN (".sql_escape_array($suppliers->keys()).") AND p.status IN ('o', 's') AND p.type IN ('k', 'p') AND (";
  if(is_numeric($search)){
    $esc_search = sql_escape_string($search);
    $qry .= "p.supplier_product_id='$esc_search' OR p.gtin_piece='$esc_search' OR p.gtin_bundle='$esc_search'";
  }else{
    $wheres = array();
    $terms = explode(' ',trim($search));
    foreach($terms as $term){
      $term = trim($term);
      if($term == ''){
        continue;
      }
      $esc_term = sql_escape_string('%'.$term.'%');
      $wheres[] = "(p.name LIKE '$esc_term' OR b.name LIKE '$esc_term')";
    }
    $qry .= implode(' AND ', $wheres);
  }
  $qry .= ") ORDER BY IF(p.status='o', 0, 1), m.producer, p.name, b.name, p.id DESC LIMIT ".intval($limit);
  #logger($qry);
  $res = sql_select_key2value($qry, 'id', 'id');
  return $res;
}

function execute_update_ajax(){
  global $user;
  $product_id = intval(get_request_param('product_id'));
  $change = get_request_param('change');
  $modus = get_request_param('modus');
  $data = get_inventory(array($product_id));
  $pdata = $data[$product_id];
  require_once('inventories.class.php');
  $inventories = new Inventories(array('product_id' => $product_id), array('id' => 'DESC'));
  #logger("execute_update_ajax ".print_r($inventories,1));
  $user_id = 0;
  $modified = '0000-00-00 00:00:00';
  if($inventories->count()){
    $inventory = $inventories->first();
    $user_id = $inventory->user_id;
    $modified = $inventory->modified;
  }
  #logger("$user_id $modified");
  if(($user_id != $user['user_id']) || (substr($modified, 0, 10) != date('Y-m-d'))){
    require_once('inventory_log.class.php');
    foreach($inventories as $inventory){
      $inventory_log = InventoryLog::create($inventory->id, $inventory->product_id, $inventory->delivery_item_id, $inventory->pickup_item_id, $inventory->user_id);
      $inventory_log->update(array(
        'modified' => $inventory->modified,
        'amount_pieces' => $inventory->amount_pieces,
        'amount_weight' => $inventory->amount_weight,
        'dividable' => $inventory->dividable,
        'weight_min' => $inventory->weight_min,
        'weight_max' => $inventory->weight_max,
        'weight_avg' => $inventory->weight_avg,
      ));
      $inventory->delete();
    }
    $inventory = Inventory::create($product_id, 0, 0, $user['user_id']);
  }
  if($change == '0'){
    $update = array('amount_pieces' => 0, 'amount_weight' => '0');
  }elseif($change == '-'){
    $amount_pieces = intval($pdata['amount_pieces']);
    $amount_pieces--;
    if($amount_pieces < 0){
      $amount_pieces = 0;
    }
    $update = array('amount_pieces' => $amount_pieces);
  }elseif($change == '+'){
    $amount_pieces = intval($pdata['amount_pieces']);
    $amount_pieces++;
    $update = array('amount_pieces' => $amount_pieces);
  }elseif($change == '='){
    $amount_pieces = intval($pdata['amount_pieces']);
    $update = array('amount_pieces' => $amount_pieces);
  }
  $update['modified'] = date('Y-m-d H:i:s');
  $inventory->update($update);
  update_inventory_product($product_id);

  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}

/*
function execute_edit(){
  $product_id = get_request_param('product_id');
  $items = new Inventories(array('product_id' => $product_id));
  foreach($items as $item){
    if(!isset($inventory)){
      $inventory = $item;
    }else{
      $inventory->amount_pieces += $item->amount_pieces;
      $inventory->amount_weight += $item->amount_weight;
    }
  }
  return array(
    'inventory' => get_inventory($product_id),
  );
}
*/

/*function get_inventory($product_id){
  $items = new Inventories(array('product_id' => $product_id));
  foreach($items as $item){
    if(!isset($inventory)){
      $inventory = $item;
    }else{
      $inventory->amount_pieces += $item->amount_pieces;
      $inventory->amount_weight += $item->amount_weight;
    }
  }
  return $inventory;
}*/

/*
function execute_products(){
  $product_ids = array();
  $items = new Inventories();
  foreach($items as $item){
    $product_ids[$item->product->id] = $item->product->id;
  }
  require_once('products.class.php');
  $products=new Products(array('type' => array('v','p','k')));
  $products = $products->getArrayCopy();
  foreach($products as $key => $product){
    if(isset($product_ids[$product->id])){
      unset($products[$key]);
    }
  }
  return array('products' => $products);
}

function execute_product_select(){
  $product_id=get_request_param('product_id');
  $id = Inventories::create(0, 0, $product_id);
  forward_to_page('/inventory/edit', 'product_id='.$product_id);
}

function execute_remove_product_ajax(){
  $product_id=get_request_param('product_id');
  require_once('sql.inc.php');
  $qry = "INSERT INTO msl_inventory_log (ts, inventory_id, delivery_item_id, pickup_item_id, product_id, amount_pieces, amount_weight, dividable, weight_min, weight_max, weight_avg, modified) SELECT NOW(), id, delivery_item_id, pickup_item_id, product_id, amount_pieces, amount_weight, dividable, weight_min, weight_max, weight_avg, modified FROM msl_inventory WHERE product_id='".sql_escape_string($product_id)."'";
  sql_update($qry);
  $qry = "DELETE FROM msl_inventory WHERE product_id='".sql_escape_string($product_id)."'";
  sql_update($qry);
  exit;
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
    $value = number_format(floatval($value), 2, '.', '');
  }

  $inventory = get_inventory($product_id);
  $diff = $value - $inventory->{$field};
  if($diff){
    $items = new Inventories(array('product_id' => $product_id, 'delivery_item_id' => 0, 'pickup_item_id' => 0));
    if(count($items)){
      $item = $items->first();
      $diff += $item->{$field};
    }else{
      $id = Inventories::create(0, 0, $product_id);
      $item = inventory_get($id);
    }
    $item->update(array( $field => $diff ));
  }

  echo json_encode(array('value' => $value));
  exit;
}
*/