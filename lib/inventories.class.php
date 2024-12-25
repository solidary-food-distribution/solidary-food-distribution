<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('inventory.class.php');

class Inventories extends Objects{
  protected $_table = 'msl_inventory';
  protected $_default_order_by = 'id';
  protected $_object_name = 'Inventory';
}
