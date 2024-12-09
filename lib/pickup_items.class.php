<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('pickup_item.class.php');

class PickupItems extends Objects{
  protected $_table = 'msl_pickup_items';
  protected $_default_order_by = 'id';
  protected $_object_name = 'PickupItem';

  public function get_product_ids(){
    $product_ids = array();
    foreach($this->array() as $item){
      $product_ids[$item->id] = $item->product_id;
    }
    return $product_ids;
  }
}