<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('deliveries');

require_once('deliveries.class.php');

function execute_index(){
  $delivery_id = get_request_param('delivery_id');
  $item_id = get_request_param('item_id');
  $delivery = delivery_get($delivery_id);
  return array('delivery' => $delivery, 'item_id' => $item_id);
}

function execute_edit(){
  $delivery_id = get_request_param('delivery_id');
  $delivery = delivery_get($delivery_id);
  require_once('products.class.php');
  $products = new Products(array('producer_id' => $delivery->supplier->id, 'type' => array('v')));
  return array(
    'delivery' => $delivery,
    'type_v' => count($products),
  );
}

function execute_item_edit(){
  $delivery_id = get_request_param('delivery_id');
  $item_id = get_request_param('item_id');
  $delivery = delivery_get($delivery_id);
  $item = $delivery->items[$item_id];
  return array(
    'delivery' => $delivery,
    'item' => $item,
  );
}

function execute_products(){
  $id=get_request_param('delivery_id');
  $item_id=get_request_param('item_id');
  $delivery=delivery_get($id);
  require_once('products.class.php');
  $products=new Products(array('producer_id' => $delivery->supplier->id, 'type' => array('v','p','k')));
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
  $item->update(array('price_type' => $product->type, 'price' => $product->price, 'price_sum' => 0, 'amount_pieces' => 0, 'amount_weight' => 0));
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
  require_once('members.class.php');
  $suppliers=new Members(array('producer' => 1));
  return array('suppliers' => $suppliers);
}

function execute_new_create(){
  global $user;
  $supplier_id=get_request_param('supplier_id');
  $delivery_id = Deliveries::create($supplier_id, $user['member_id']);
  forward_to_page('/delivery/edit', 'delivery_id='.$delivery_id);
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
    $delivery = delivery_get($delivery_id);
    $item = $delivery->items[$item_id];
    $updates = array($field => $value);
    #logger('item '.print_r($item,1));
    if($item->product->type == 'p' || $item->product->type == 'k'){
      $updates['price_sum'] = $item->price * $value;
    }
    $item->update($updates);
    inventory_update($item->id, $item->product_id, array($field => $value));
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