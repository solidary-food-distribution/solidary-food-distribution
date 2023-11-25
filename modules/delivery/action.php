<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('deliveries');

function execute_index(){
  $id = get_request_param('id');
  $edit = get_request_param('edit');
  $item_id = get_request_param('item_id');
  $delivery = delivery_get($id);
  $suppliers='';
  if($edit){
    require_once('members.class.php');
    $suppliers=new Members(array('producer' => 1));
  }
  return array('delivery' => $delivery, 'edit' => $edit, 'suppliers' => $suppliers, 'item_id' => $item_id);
}

function execute_head_ajax(){
  $return = execute_index();
  if($return['edit']){
    $return['template'] = 'head_edit.part.php';
  }else{
    $return['template'] = 'head.part.php';
  }
  $return['layout'] = 'layout_null.php';
  return $return;
}

function execute_item_ajax(){
  $delivery_id=get_request_param('delivery_id');
  $item_id=get_request_param('item_id');
  $edit=get_request_param('edit');
  if($edit){
    $template='item_edit.part.php';
  }else{
    $template='item.part.php';
  }
  $delivery=delivery_get($delivery_id);
  $item=$delivery->items[$item_id];
  return array(
    'delivery' => $delivery,
    'item' => $item,
    'template' => $template,
    'layout' => 'layout_null.php',
  );
}

function execute_products(){
  $id=get_request_param('delivery_id');
  $item_id=get_request_param('item_id');
  $delivery=delivery_get($id);
  require_once('products.class.php');
  $filter=array();
  $products=new Products(array('producer_id' => $delivery->supplier->id, 'type' => array('v','p','k')));
  return array('delivery'=>$delivery,'products'=>$products,'item_id'=>$item_id);
}

function execute_product_select(){
  $id=get_request_param('delivery_id');
  $item_id=get_request_param('item_id');
  $product_id=get_request_param('product_id');
  $delivery=delivery_get($id);
  if($item_id){
    $delivery->items[$item_id]->update(array('product_id' => $product_id));
  }else{
    $item = $delivery->item_create($product_id);
    $item_id = $item->id;
  }
  forward_to_page('/delivery', 'id='.$id.'&item_id='.$item_id);
}

function execute_item_delete_ajax(){
  $id=get_request_param('delivery_id');
  $item_id=get_request_param('item_id');
  $delivery=delivery_get($id);
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
  require_once('deliveries.class.php');
  $delivery_id = Deliveries::create($supplier_id, $user['member_id']);
  forward_to_page('/delivery', 'id='.$delivery_id.'&edit=1');
}

function delivery_get($id){
  require_once('deliveries.class.php');
  $deliveries = new Deliveries(array('id' => $id));
  if(!empty($deliveries)){
    return $deliveries->first();
  }
  return null;
}