<?php

function update_inventory(){

  $idis = array();
  $ipuis = array();
  require_once('inventories.class.php');
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
        $inventory = Inventory::create($di->product_id, 0);
        $inventory->update(array(
          'delivery_item_id' => $di->id,
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
      $inventory = Inventory::create($pui->product_id, 0);
      $inventory->update(array(
        'pickup_item_id' => $pui->id,
        'amount_pieces' => -$pui->amount_pieces,
        'amount_weight' => -$pui->amount_weight,
      ));
    }
  }
}