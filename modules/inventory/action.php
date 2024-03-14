<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('inventory');

require_once('inventories.class.php');

function execute_index(){
  $items = new Inventories();
  $inventories = array();
  foreach($items as $item){
    if(!isset($inventories[$item->product->id])){
      $inventories[$item->product->id] = $item;
    }else{
      $inventories[$item->product->id]->amount_pieces += $item->amount_pieces;
      $inventories[$item->product->id]->amount_weight += $item->amount_weight;
    }
  } 
  return array('inventories' => $inventories);
}

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
    'inventory' => get_inventory($product_id, false),
  );
}

function get_inventory($product_id){
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
}


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
