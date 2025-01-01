<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('deliveries');

require_once('deliveries.class.php');
require_once('delivery_items.class.php');

function execute_index(){
  $delivery_id = get_request_param('delivery_id');
  $item_id = get_request_param('item_id');
  
  $delivery = Deliveries::sget($delivery_id);
  
  require_once('members.class.php');
  $suppliers = new Members(array('id' => $delivery->supplier_id));
  $supplier = $suppliers->first();

  $delivery_items = new DeliveryItems(array('delivery_id' => $delivery_id));
  
  $product_ids = $delivery_items->get_product_ids();
  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));

  return array('delivery' => $delivery, 'delivery_items' => $delivery_items, 'supplier' => $supplier, 'products' => $products, 'item_id' => $item_id);
}

function execute_edit(){
  $delivery_id = get_request_param('delivery_id');
  $delivery = Deliveries::sget($delivery_id);
  require_once('members.class.php');
  $suppliers = new Members(array('id' => $delivery->supplier_id));
  return array(
    'delivery' => $delivery,
    'supplier' => $suppliers->first(),
    #'type_v' => count($products),
  );
}

function execute_item_edit(){
  $delivery_id = get_request_param('delivery_id');
  $item_id = get_request_param('item_id');

  $delivery = Deliveries::sget($delivery_id);
  require_once('members.class.php');
  $suppliers = new Members(array('id' => $delivery->supplier_id));
  $item = DeliveryItems::sget($item_id);

  require_once('products.class.php');
  $product = Products::sget($item->product_id);
  return array(
    'delivery' => $delivery,
    'supplier' => $suppliers->first(),
    'item' => $item,
    'product' => $product,
  );
}

function execute_products(){
  $delivery_id=get_request_param('delivery_id');
  $item_id=get_request_param('item_id');
  $delivery = Deliveries::sget($delivery_id);
  require_once('products.class.php');
  $products=new Products(array('producer_id' => $delivery->supplier_id, 'type' => array('v','p','k')));
  return array('delivery'=>$delivery,'products'=>$products,'item_id'=>$item_id);
}

function execute_product_select(){
  $delivery_id=get_request_param('delivery_id');
  $item_id=get_request_param('item_id');
  $product_id=get_request_param('product_id');
  $delivery=delivery_get($delivery_id);
  if($item_id){
    $delivery->items[$item_id]->update(array('product_id' => $product_id));
    $item = $delivery->items[$item_id];
  }else{
    $item = $delivery->item_create($product_id);
    $item_id = $item->id;
  }

  require_once('products.class.php');
  $product = product_get($product_id);
  $price_type = $product->type;
  $purchase = 0;

  $last = new Deliveries(array('di.product_id' => $product_id, 'di.price_type!=' => ''), array('d.id' => 'DESC'), 0, 1);
  if(count($last)){
    $last = $last->first();
    if(count($last->items)){
      $last = $last->items[key($last->items)];
      $price_type = $last->price_type;
      $purchase = $last->purchase;
    }
  }

  $item->update(array('price_type' => $price_type, 'purchase' => $purchase, 'purchase_sum' => 0, 'amount_pieces' => 0, 'amount_weight' => 0));
  forward_to_page('/delivery/item_edit', 'delivery_id='.$delivery_id.'&item_id='.$item_id);
}

function execute_delete_ajax(){
  $id=get_request_param('delivery_id');
  $delivery=delivery_get($id);
  $updates = array( 'delete' => 1 );
  foreach($delivery->items as $item){
    inventory_update($item->id, $item->product_id, $updates);
  }
  $delivery->delete($id);
  exit;
}

function execute_item_delete_ajax(){
  $id=get_request_param('delivery_id');
  $item_id=get_request_param('item_id');
  $delivery=delivery_get($id);
  $item = $delivery->items[$item_id];
  $updates = array( 'delete' => 1 );
  inventory_update($item_id, $item->product_id, $updates);
  $delivery->item_delete($item_id);
  exit;
}

function execute_new(){
  $pickup_date = '2025-01-10';
  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date' => $pickup_date));
  $order_ids = $orders->keys();
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $order_ids));
  $product_ids = array();
  foreach($order_items as $order_item){
    if($order_item->amount_pieces || $order_item->amount_weight){
      $product_ids[$order_item->product_id] = 1;
    }
  }
  require_once('products.class.php');
  $products = new Products(array('id' => array_keys($product_ids)));
  $supplier_ids = array();
  foreach($products as $product){
    $supplier_ids[$product->supplier_id] = 1;
  }

  require_once('members.class.php');
  $suppliers=new Members(array('id' => array_keys($supplier_ids)));
  return array('suppliers' => $suppliers);
}

function execute_new_create(){
  global $user;
  $supplier_id=get_request_param('supplier_id');
  $delivery_id = Deliveries::create($supplier_id, $user['member_id']);
  $delivery = delivery_get($delivery_id);
  $pickup_date = '2025-01-10';
  require_once('orders.class.php');
  $orders = new Orders(array('pickup_date' => $pickup_date));
  $order_ids = $orders->keys();
  require_once('order_items.class.php');
  $order_items = new OrderItems(array('order_id' => $order_ids));
  $product_ids = array(0 => 0);
  $product_amounts = array();
  foreach($order_items as $order_item){
    if($order_item->amount_pieces || $order_item->amount_weight){
      $product_ids[$order_item->product_id] = 1;
      $product_amounts[$order_item->product_id]['amount_pieces'] += $order_item->amount_pieces;
      $product_amounts[$order_item->product_id]['amount_weight'] += $order_item->amount_weight;
    }
  }
  require_once('products.class.php');
  $products = new Products(array('id' => array_keys($product_ids)));
  foreach($products as $product_id => $product){
    if($product->supplier_id == $supplier_id){
      $item = $delivery->item_create($product_id);
      if($product->supplier_id == 35 && $product->amount_per_bundle > 1){
        $amount = $product_amounts[$product_id]['amount_pieces'];
        $product_amounts[$product_id]['amount_bundles'] = ceil($amount / $product->amount_per_bundle);
        $product_amounts[$product_id]['amount_pieces'] = ceil($amount / $product->amount_per_bundle) * $product->amount_per_bundle;
      }
      $item->update($product_amounts[$product_id]);
    }
  }

  forward_to_page('/delivery/index', 'delivery_id='.$delivery_id);
}

function execute_update_ajax(){
  $delivery_id = get_request_param('delivery_id');
  $item_id = get_request_param('item_id');
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
  if($item_id){
    $item = DeliveryItems::sget($item_id);
    $updates = array($field => $value);
    #logger('item '.print_r($item,1));
    if($item->product->type == 'p' || $item->product->type == 'k'){
      $updates['price_sum'] = $item->price * $value;
    }
    $item->update($updates);
    #inventory_update($item->id, $item->product_id, array($field => $value));
  }else if($delivery_id){
    $delivery = delivery_get($delivery_id);
    $delivery->update(array($field => $value));
  }
  echo json_encode(array('value' => $value));
  exit;
}

function inventory_update($delivery_item_id, $product_id, $updates){
  require_once('inventories.class.php');
  $objects = new Inventories(array('delivery_item_id' => $delivery_item_id));
  if(count($objects)){
    $inventory = $objects->first();
  }elseif(!isset($updates['delete'])){
    $id = Inventories::create($delivery_item_id, 0, $product_id);
    $inventory = inventory_get($id);
  }
  if(isset($inventory) && isset($updates['delete'])){
    $inventory->delete();
    return;
  }
  foreach($updates as $field=>$value){
    if(!isset($inventory->{$field})){
      unset($updates[$field]);
    }
  }
  if(!empty($updates)){
    $inventory->update($updates);
  }
}