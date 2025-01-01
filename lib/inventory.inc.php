<?php

require_once('inventories.class.php');

function update_inventory(){

  $delivery_item_modified = '0000-00-00 00:00:00';
  $inventories = new Inventories(array('delivery_item_id>' => 0), array('modified' => 'DESC'), 0, 1);
  if($inventories->count()){
    $delivery_item_modified = $inventories->first()->modified;
  }

/*
  require_once('deliveries.class.php');
  $deliveries = new Deliveries(array('modified>=' => $delivery_item_modified));
  foreach($deliveries as $delivery){

  }
*/


  $pickup_item_modified = '';
  $inventories = new Inventories(array('pickup_item_id>' => 0), array('modified' => 'DESC'), 0, 1);
  if($inventories->count()){
    $pickup_item_modified = $inventories->first()->modified;
  }



  logger(print_r($inventories,1));
  return;

  $idis = array();
  $ipuis = array();
  
  $inventories = new Inventories(array('user_id>' => 0), array('created' => 'ASC'));
  foreach($inventories as $inventory){
    if($inventory->delivery_item_id){
      $idis[$inventory->delivery_item_id] = $inventory;
    }
    if($inventory->pickup_item_id){
      $ipuis[$inventory->pickup_item_id] = $inventory;
    }
  }

  require_once('deliveries.class.php');
  $deliveries = new Deliveries();
  foreach($deliveries as $delivery){
    foreach($delivery->items as $di){
      if(!$di->amount_pieces && !$di->amount_weight){
        continue;
      }
      if(!isset($idis[$di->id])){
        $inventory = Inventory::create($di->product_id, $di->id, 0, 0);
        $inventory->update(array(
          'amount_pieces' => $di->amount_pieces,
          'amount_weight' => $di->amount_weight,
          'dividable' => $di->dividable,
          'weight_min' => $di->weight_min,
          'weight_max' => $di->weight_max,
          'weight_avg' => $di->weight_avg,
        ));
      }
    }
  }

  require_once('pickups.class.php');
  $pickups = new Pickups();

  require_once('pickup_items.class.php');
  $pickup_items = new PickupItems(array('pickup_id' => $pickups->keys()));
  foreach($pickup_items as $pui){
    if(!$pui->amount_pieces && !$pui->amount_weight){
      continue;
    }
    if(!isset($ipuis[$pui->id])){
      $inventory = Inventory::create($pui->product_id, 0, $pui->id, 0);
      $inventory->update(array(
        'amount_pieces' => -$pui->amount_pieces,
        'amount_weight' => -$pui->amount_weight,
      ));
    }
  }
}