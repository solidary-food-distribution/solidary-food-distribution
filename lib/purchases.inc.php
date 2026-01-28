<?php

#debug
#require_once('inc.php');
#purchases_get_product_sums('2025-05-16',13);

function purchases_get_product_sums($pickup_date, $supplier_id){
  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date' => $pickup_date));
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $orders->keys()));
  require_once('products.class.php');
  $products = new Products(array('id' => $order_items->get_product_ids(), 'supplier_id' => $supplier_id));

  $order_amounts = array();
  foreach($order_items as $order_item){
    if(!isset($products[$order_item->product_id]) || (!$order_item->amount_pieces && !$order_item->amount_weight)){
      continue;
    }
    #logger("purchases_get_product_sums order_item ".print_r($order_item,1));
    if($order_item->split_status == 'n'){
      $order_amounts[$order_item->product_id]['amount_pieces'] = $order_amounts[$order_item->product_id]['amount_pieces'] + $order_item->amount_pieces;
      $order_amounts[$order_item->product_id]['amount_weight'] = $order_amounts[$order_item->product_id]['amount_weight'] + $order_item->amount_weight;
    }elseif($order_item->split_status == 'o'){
      $split_data = json_decode($order_item->split_data, 1);
      $order_amounts[$order_item->product_id]['amount_pieces'] = $order_amounts[$order_item->product_id]['amount_pieces'] + $split_data['ordered'];
    }
  }
  #logger("purchases_get_product_sums order_amounts ".print_r($order_amounts,1));

  require_once('inventory.inc.php');
  $inventory = get_inventory(array_keys($order_amounts));
  if($supplier_id!=35){
    $inventory=array(); //only Oekoring
  }

  foreach($inventory as $product_id => $pi){
    if($products[$product_id]->status!='o' && $products[$product_id]->status!='e'){
      unset($inventory[$product_id]); //workaround
    }
  }

  #logger("purchases_get_product_sums inventory ".print_r($inventory,1));

  foreach($order_amounts as $product_id => $amounts){
    $order_amounts[$product_id]['amount_pieces_needed'] = $order_amounts[$product_id]['amount_pieces'];
    $order_amounts[$product_id]['amount_weight_needed'] = $order_amounts[$product_id]['amount_weight'];
    $order_amounts[$product_id]['amount_pieces_inventory'] = $inventory[$product_id]['amount_pieces'];
    $order_amounts[$product_id]['amount_weight_inventory'] = $inventory[$product_id]['amount_weight'];
    if(isset($inventory[$product_id]) && ($products[$product_id]->type == 'p' || $products[$product_id]->type == 'w') && $inventory[$product_id]['amount_pieces'] >= $amounts['amount_pieces']){
      $order_amounts[$product_id]['amount_pieces'] = 0;
    }elseif(isset($inventory[$product_id]) && $products[$product_id]->type == 'k' && $inventory[$product_id]['amount_weight'] >= $amounts['amount_weight']){
      $order_amounts[$product_id]['amount_weight'] = 0;
    }else{
      $order_amounts[$product_id]['amount_pieces'] -= $inventory[$product_id]['amount_pieces'];
      $order_amounts[$product_id]['amount_weight'] -= $inventory[$product_id]['amount_weight'];
    }
  }
  #logger("purchases_get_product_sums order_amounts ".print_r($order_amounts,1));

  require_once('prices.class.php');
  $prices = new Prices(array('product_id' => array_keys($order_amounts), 'start<=' => $pickup_date, 'end>=' => $pickup_date));
  foreach($order_amounts as $product_id => $amount){
    if($amount['amount_pieces'] <= 0 && $amount['amount_weight'] <= 0){
      continue;
    }
    $amount_order = 0;
    if($amount['amount_pieces']>0 && $products[$product_id]->type=='p'){
      if($prices[$product_id]->amount_per_bundle > 1){
        $amount_bundles = ceil($amount['amount_pieces'] / $prices[$product_id]->amount_per_bundle);
        $amount_order = $amount_bundles * $prices[$product_id]->amount_per_bundle;
        $order_amounts[$product_id]['amount_pieces'] = $amount_order;
        $order_amounts[$product_id]['amount_bundles'] = $amount_bundles;
      }else{
        $amount_order = $amount['amount_pieces'];
      }
    }elseif($amount['amount_weight'] > 0){
      $amount_order = $amount['amount_weight'];
    }
    if($products[$product_id]->type == 'p'){
      $order_amounts[$product_id]['price_type'] = 'p';
    }else{
      $order_amounts[$product_id]['price_type'] = 'k';
    }
    $order_amounts[$product_id]['purchase'] = $prices[$product_id]->purchase;
    $order_amounts[$product_id]['purchase_sum'] = round($amount_order * $prices[$product_id]->purchase, 3);
  }

  return $order_amounts;
}


function purchases_get_sum($pickup_date, $supplier_id){
  $order_amounts = purchases_get_product_sums($pickup_date, $supplier_id);
  $purchases_sum = 0;
  foreach($order_amounts as $amount){
    $purchases_sum += $amount['purchase_sum'];
  }
  return $purchases_sum;
}