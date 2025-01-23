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

  require_once('sql.class.php');
  $brands = SQL::selectKey2Val("SELECT id, name FROM msl_brands", 'id', 'name');

  return array('delivery' => $delivery, 'delivery_items' => $delivery_items, 'supplier' => $supplier, 'brands' => $brands, 'products' => $products, 'item_id' => $item_id);
}

function execute_products(){
  echo "needs refactor";exit;
  /*
  $delivery_id=get_request_param('delivery_id');
  $item_id=get_request_param('item_id');
  $delivery = Deliveries::sget($delivery_id);
  require_once('products.class.php');
  $products=new Products(array('producer_id' => $delivery->supplier_id, 'type' => array('v','p','k')));
  return array('delivery'=>$delivery,'products'=>$products,'item_id'=>$item_id);
  */
}

function execute_product_select(){
  echo "needs refactor";exit;
  /*
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
  */
}

function execute_delete_ajax(){
  $delivery_id = get_request_param('delivery_id');
  require_once('inventory.inc.php');
  $delivery_items = new DeliveryItems(array('delivery_id' => $delivery_id));
  foreach($delivery_items as $delivery_item){
    $delivery_item->delete();
    require_once('inventory.inc.php');
    update_inventory_product($delivery_item->product_id);
  }
  $delivery = Deliveries::sget($delivery_id);
  $delivery->delete($delivery_id);
  exit;
}


function execute_new(){
  require_once('delivery_dates.class.php');
  $delivery_dates = new DeliveryDates(array('date>=' => date('Y-m-d')), array('date' => 'ASC'), 0, 1);
  $delivery_date = $delivery_dates->first();

  $pickup_date = $delivery_date->date;
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
  $delivery_id = Delivery::create($supplier_id, $user['user_id']);
  $delivery = Deliveries::sget($delivery_id);
  $pickup_date = '2025-01-24';
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
      $item = DeliveryItem::create($delivery_id, $product_id);
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


function execute_change_ajax(){
  global $user;
  $delivery_id = get_request_param('delivery_id');
  $product_id = intval(get_request_param('product_id'));
  $change = get_request_param('change');
  logger("$delivery_id $product_id $change");
  if($delivery_id && $product_id && $change){
    require_once('delivery_items.class.php');
    $delivery_items = new DeliveryItems(array('delivery_id' => $delivery_id, 'product_id' => $product_id));
    if(!$delivery_items->count()){
      $delivery_item = DeliveryItems::create($delivery_id, $product_id);
    }else{
      $delivery_item = $delivery_items->first();
    }
    require_once('products.class.php');
    $product = Products::sget($product_id);
    if($product->type == 'p' || $product->type == 'w'){
      $amount = $delivery_item->amount_pieces;
      $amount_field = 'amount_pieces';
    }elseif($product->type == 'k'){
      $amount = $delivery_item->amount_weight;
      $amount_field = 'amount_weight';
    }else{
      throw new Exception("unknown product-type ".print_r($product,1), 1);
      exit;
    }

    if($change == '+' && $product->supplier_id == 35){
      $amount_new = round($amount + $product->amount_per_bundle, 3);
    }elseif($change == '+'){
      $amount_new = round($amount + $product->amount_steps, 3);
    }elseif($change == '-' && $product->supplier_id == 35){
      $amount_new = round($amount - $product->amount_per_bundle, 3);
    }elseif($change == '-'){
      $amount_new = round($amount - $product->amount_steps, 3);
    }

    if($amount_new < 0){
      $amount_new = 0;
    }
    logger("amount_field $amount_field amount_new $amount_new");
    $delivery_item->update(array($amount_field => $amount_new));
    require_once('inventory.inc.php');
    update_inventory_product($product_id);
  }
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}

function execute_add_product_ajax(){
  $delivery_id = intval(get_request_param('delivery_id'));
  $supplier_product_id = trim(get_request_param('supplier_product_id'));
  $delivery = Deliveries::sget($delivery_id);
  $error = '';
  require_once('products.class.php');
  $products = new Products(array('supplier_id' => $delivery->supplier_id, 'supplier_product_id' => $supplier_product_id));
  if(!count($products)){
    $error = "Artikelnummer ".$supplier_product_id." nicht gefunden";
  }
  if($error == ''){
    $product = $products->first();
    require_once('delivery_items.class.php');
    $delivery_items = new DeliveryItems(array('delivery_id' => $delivery->id, 'product_id' => $product->id));
    if(!count($delivery_items)){
      $delivery_item = DeliveryItem::create($delivery->id, $product->id);
    }else{
      $delivery_item = $delivery_items->first();
      $error = 'Produkt mit Artikelnummer '.$supplier_product_id.' ist bereits in der Liste.';
    }
    $supplier_product_id = '';
    set_request_param('item_id', $delivery_item->id);
  }
  $return=execute_index();
  $return['supplier_product_id'] = $supplier_product_id;
  $return['error'] = $error;
  $return['template'] = 'index.php';
  $return['layout'] = 'layout_null.php';
  return $return;
}

function execute_scale_ajax(){
  global $user;
  $delivery_id = intval(get_request_param('delivery_id'));
  $delivery_item_id = intval(get_request_param('item_id'));
  $value = get_request_param('value');
  $delivery = Deliveries::sget($delivery_id);
  require_once('delivery_items.class.php');
  $delivery_item = DeliveryItems::sget($delivery_item_id);
  $delivery_item->update(array('amount_weight' => floatval($value)));
  require_once('inventory.inc.php');
  update_inventory_product($delivery_item->product_id);
  $return=execute_index();
  $return['template']='index.php';
  $return['layout']='layout_null.php';
  return $return;
}