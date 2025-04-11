<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('deliveries');

function execute_index(){
  $date = get_request_param('date');

  require_once('deliveries.class.php');
  $date_prev = '';
  if($date == ''){
    $ds = new Deliveries(array('created<=' => date('Y-m-d')), array('created' => 'DESC'), 0, 10);
    foreach($ds as $d){
      if($date == ''){
        $date = substr($d->created, 0, 10);
      }
      if($date_prev == '' && substr($d->created, 0, 10) < $date){
        $date_prev = substr($d->created, 0, 10);
        break;
      }
    }
  }

  if($date_prev == ''){
    $ds = new Deliveries(array('created<' => $date), array('created' => 'DESC'), 0, 1);
    if($ds->count()){
      $date_prev = substr($ds->first()->created, 0, 10);
    }
  }

  $date_next = '';
  $ds = new Deliveries(array('created>' => $date.' 23:59:59'), array('created' => 'ASC'), 0, 1);
  if($ds->count()){
    $date_next = substr($ds->first()->created, 0, 10);
  }

  $deliveries = array();
  $ds = new Deliveries(array('created>=' => $date), array('created' => 'ASC', 'supplier_id' => 'ASC') , 0, 10);
  foreach($ds as $id => $d){
    if(substr($d->created,0, 10) > $date){
      break;
    }
    $deliveries[$id] = $d;
  }

  require_once('delivery_items.class.php');
  $dis = new DeliveryItems(array('delivery_id' => array_keys($deliveries)));
  $delivery_items = array();
  foreach($dis as $di){
    if($di->amount_pieces || $di->amount_weight){
      $delivery_items[$di->delivery_id][$di->id] = $di;
    }
  }
  $product_ids = $dis->get_product_ids();

  require_once('products.class.php');
  $products = new Products(array('id' => $product_ids));

  require_once('members.class.php');
  $suppliers = new Members(array('producer' => array(1,2)));

  return array('date' => $date, 'date_prev' => $date_prev, 'date_next' => $date_next, 'deliveries'=>$deliveries, 'delivery_items' => $delivery_items, 'suppliers' => $suppliers, 'products' => $products);
}