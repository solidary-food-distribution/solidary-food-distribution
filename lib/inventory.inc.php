<?php

require_once('inventories.class.php');

function update_inventory(){
  require_once('products.class.php');
  $products = new Products(array('status' => array('o', 'e'), 'supplier_id' => array(35)));
  $products_ids = $products->keys();

  $product_ids = array();

  $delivery_item_modified = '0000-00-00 00:00:00';
  $inventories = new Inventories(array('delivery_item_id>' => 0), array('modified' => 'DESC'), 0, 1);
  if($inventories->count()){
    $delivery_item_modified = $inventories->first()->modified;
  }
  require_once('delivery_items.class.php');
  $delivery_items = new DeliveryItems(array('modified>=' => $delivery_item_modified, 'product_id' => $products_ids));
  foreach($delivery_items as $delivery_item){
    $product_ids[$delivery_item->product_id] = 1;
  }

  $pickup_item_modified = '0000-00-00 00:00:00';
  $inventories = new Inventories(array('pickup_item_id>' => 0), array('modified' => 'DESC'), 0, 1);
  if($inventories->count()){
    $pickup_item_modified = $inventories->first()->modified;
  }
  require_once('pickup_items.class.php');
  $pickup_items = new PickupItems(array('modified>=' => $pickup_item_modified, 'product_id' => $products_ids));
  foreach($pickup_items as $pickup_item){
    $product_ids[$pickup_item->product_id] = 1;
  }

  foreach($product_ids as $product_id => $dummy){
    update_inventory_product($product_id);
  }
}

function update_inventory_product($product_id){
  $user_modified = '0000-00-00 00:00:00';
  $inventories = new Inventories(array('user_id>' => 0, 'product_id' => $product_id), array('modified' => 'DESC'), 0, 1);
  if($inventories->count()){
    $user_modified = $inventories->first()->modified;
  }
  #logger("update_inventory_product $product_id user_modified $user_modified ".print_r($inventories,1));

  $delivery_item_modified = '0000-00-00 00:00:00';
  $inventories = new Inventories(array('delivery_item_id>' => 0, 'product_id' => $product_id, 'modified>' => $user_modified), array('modified' => 'DESC'), 0, 1);
  if($inventories->count()){
    $delivery_item_modified = $inventories->first()->modified;
  }
  require_once('delivery_items.class.php');
  $delivery_items = new DeliveryItems(array('modified>=' => $delivery_item_modified, 'product_id' => $product_id, 'modified>' => $user_modified));
  $inventories = new Inventories(array('delivery_item_id' => $delivery_items->keys()));
  $idis = array();
  foreach($inventories as $inventory){
    $idis[$inventory->delivery_item_id] = $inventory;
  }
  foreach($delivery_items as $delivery_item){
    if(!isset($idis[$delivery_item->id])){
      $idis[$delivery_item->id] = Inventory::create($delivery_item->product_id, $delivery_item->id, 0, 0);
    }
    $idis[$delivery_item->id]->update(array(
      'modified' => $delivery_item->modified,
      'amount_pieces' => $delivery_item->amount_pieces,
      'amount_weight' => $delivery_item->amount_weight,
      'dividable' => $delivery_item->dividable,
      'weight_min' => $delivery_item->weight_min,
      'weight_max' => $delivery_item->weight_max,
      'weight_avg' => $delivery_item->weight_avg,
    ));
  }

  $pickup_item_modified = '0000-00-00 00:00:00';
  $inventories = new Inventories(array('pickup_item_id>' => 0, 'product_id' => $product_id, 'modified>' => $user_modified), array('modified' => 'DESC'), 0, 1);
  if($inventories->count()){
    $pickup_item_modified = $inventories->first()->modified;
  }
  require_once('pickup_items.class.php');
  $pickup_items = new PickupItems(array('modified>=' => $pickup_item_modified, 'product_id' => $product_id, 'modified>' => $user_modified));
  $inventories = new Inventories(array('pickup_item_id' => $pickup_items->keys()));
  $ipis = array();
  foreach($inventories as $inventory){
    $ipis[$inventory->pickup_item_id] = $inventory;
  }
  foreach($pickup_items as $pickup_item){
    if(!isset($ipis[$pickup_item->id])){
      $ipis[$pickup_item->id] = Inventory::create($pickup_item->product_id, 0, $pickup_item->id, 0);
    }
    $ipis[$pickup_item->id]->update(array(
      'modified' => $pickup_item->modified,
      'amount_pieces' => -$pickup_item->amount_pieces,
      'amount_weight' => -$pickup_item->amount_weight,
    ));
  }

  if($user_modified != '0000-00-00 00:00:00'){
    $inventories = new Inventories(array('product_id' => $product_id, 'modified<' => $user_modified));
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
  }
}


function get_inventory($product_ids = array()){
  $filter = array();
  if(!empty($product_ids)){
    $filter = array('product_id' => $product_ids);
  }
  $inventories = new Inventories($filter);
  $data = array();
  foreach($inventories as $inventory){
    $data[$inventory->product_id]['amount_pieces'] = $data[$inventory->product_id]['amount_pieces'] + $inventory->amount_pieces;
    $data[$inventory->product_id]['amount_weight'] = $data[$inventory->product_id]['amount_weight'] + $inventory->amount_weight;
    $data[$inventory->product_id]['user_id'] = $inventory->user_id;
  }
  return $data;
}