<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('inventory_log.class.php');

class InventoryLogs extends Objects{
  protected $_table = 'msl_inventory_log';
  protected $_default_order_by = 'id';
  protected $_object_name = 'InventoryLog';
}
