<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('order_item.class.php');

class OrderItems extends Objects{
  protected $_table = 'msl_order_items';
  protected $_default_order_by = 'id';
  protected $_object_name = 'OrderItem';

  public function get_product_ids(){
    $product_ids = array();
    foreach($this->array() as $order_item){
      $product_ids[$order_item->id] = $order_item->product_id;
    }
    return $product_ids;
  }
}