<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('order.class.php');

class Orders extends Objects{
  protected $_table = 'msl_orders';
  protected $_default_order_by = 'pickup_date';
  protected $_object_name = 'Order';

  
}