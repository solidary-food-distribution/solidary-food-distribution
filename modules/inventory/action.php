<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('inventory');

require_once('inventory.inc.php');

function execute_index(){
  $modus = get_request_param('modus');
  if(empty($modus)){
    $modus = '1';
  }

  update_inventory();
  $data = get_inventory();

  if($modus == 's' && trim($search) == ''){
    $products = array();
  }elseif($modus == 's'){
    require_once('members.class.php');
    $suppliers = new Members(array('producer' => 2));
    $product_ids = search_products($search, $suppliers, $limit);
    $products = new Products(array('id' => $product_ids),array('FIELD(id,'.implode(',',$product_ids).')' => 'ASC'));
  }else{
    require_once('members.class.php');
    $suppliers = new Members(array('producer' => $modus));
    $product_ids = array();
    foreach($data as $product_id => $pdata){
      if($pdata['user_id'] == 0 || $pdata['amount_pieces'] != 0 || $pdata['amount_pieces'] != 0){
        $product_ids[] = $product_id;
      }
    }
    require_once('products.class.php');
    $products = new Products(array('id' => $product_ids, 'supplier_id' => $suppliers->keys()), array('FIELD(type,\'k\',\'w\',\'p\')' => '', 'name' => 'ASC'));
  }

  if(!isset($suppliers)){
    require_once('members.class.php');
    $suppliers = new Members(array('producer>=' => 1));
  }

  require_once('sql.class.php');
  $brands = SQL::selectKey2Val("SELECT id, name FROM msl_brands", 'id', 'name');

  return array(
    'data' => $data,
    'products' => $products,
    'modus' => $modus,
    'suppliers' => $suppliers,
    'brands' => $brands,
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

function execute_update_ajax(){
  global $user;
  $product_id = intval(get_request_param('product_id'));
  $change = get_request_param('change');
  $modus = get_request_param('modus');
  $data = get_inventory(array($product_id));
  $pdata = $data[$product_id];
  require_once('inventories.class.php');
  $inventories = new Inventories(array('product_id' => $product_id), array('id' => 'DESC'), 0, 1);
  $user_id = 0;
  if($inventories->count()){
    $inventory = $inventories->first();
    $user_id = $inventory->user_id;
  }
  if(!$user_id){
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
  require_once('sql.class.php');
  $qry = "INSERT INTO msl_inventory_log (ts, inventory_id, delivery_item_id, pickup_item_id, product_id, amount_pieces, amount_weight, dividable, weight_min, weight_max, weight_avg, modified) SELECT NOW(), id, delivery_item_id, pickup_item_id, product_id, amount_pieces, amount_weight, dividable, weight_min, weight_max, weight_avg, modified FROM msl_inventory WHERE product_id='".SQL::escapeString($product_id)."'";
  SQL::update($qry);
  $qry = "DELETE FROM msl_inventory WHERE product_id='".SQL::escapeString($product_id)."'";
  SQL::update($qry);
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