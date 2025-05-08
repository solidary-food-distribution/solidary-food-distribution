<?php

require_once('pickups.class.php');

function pickup_get($pickup_id, $member_id){
  $pickups = new Pickups(array('id' => $pickup_id, 'member_id' => $member_id));
  if(count($pickups)){
    return $pickups->first();
  }
  return null;
}

function update_pickup_items($pickup_id){
  global $user;
  $pickup = pickup_get($pickup_id, $user['member_id']);
  if($pickup->status != 'o'){
    return;
  }

  require_once('delivery_dates.class.php');
  $delivery_dates = new DeliveryDates(array('date<=' => $pickup->created), array('date' => 'DESC'), 0, 1);
  $delivery_date = $delivery_dates->first();
  $pickup_date = $delivery_date->date;

  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date' => $pickup_date, 'member_id' => $user['member_id']));
  if(!count($orders)){
    return;
  }
  $order = $orders->first();
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $order->id));

  require_once('products.class.php');
  $products = new Products(array('id' => $order_items->get_product_ids()));

  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => $order_items->get_product_ids(), 'start<=' => $order->pickup_date, 'end>=' => $order->pickup_date));

  require_once('pickup_items.class.php');
  $puis = new PickupItems(array('pickup_id' => $pickup_id));
  $pickup_items = array();
  foreach($puis as $pui){
    $pickup_items[$pui->product_id] = $pui;
  }

  require_once('inventory.inc.php');

  foreach($order_items as $oi){
    if(!$oi->amount_pieces && !$oi->amount_weight){
      continue;
    }
    if(!isset($pickup_items[$oi->product_id])){
      $pui = PickupItem::create($pickup_id, $oi->product_id);
      $updates = array(
        'order_item_id' => $oi->id,
        'amount_pieces_min' => $oi->amount_pieces,
        'amount_pieces_max' => $oi->amount_pieces,
        'amount_weight_min' => $oi->amount_weight * 0.9,
        'amount_weight_max' => $oi->amount_weight * 1.1,
        'price' => $prices[$oi->product_id]->price,
        'amount_per_bundle' => $prices[$oi->product_id]->amount_per_bundle,
        'price_bundle' => $prices[$oi->product_id]->price_bundle,
        'tax' => $prices[$oi->product_id]->tax,
      );
      if($products[$oi->product_id]->type == 'p'){
        $updates['price_type'] = 'p';
      }else{ //k or w
        $updates['price_type'] = 'k';
      }
      $pui->update($updates);
      update_inventory_product($product_id);
      $pui = PickupItems::sget($pui->id);
      $pickup_items[$oi->product_id] = $pui;
    }
  }
}

