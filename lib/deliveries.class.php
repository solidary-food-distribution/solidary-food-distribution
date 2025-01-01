<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('delivery.class.php');

class Deliveries extends Objects{
  protected $_table = 'msl_deliveries';
  protected $_default_order_by = 'id';
  protected $_object_name = 'Delivery';
}
