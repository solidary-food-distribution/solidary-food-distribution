<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('pickup.class.php');

class Pickups extends Objects{
  protected $_table = 'msl_pickups';
  protected $_default_order_by = 'id';
  protected $_object_name = 'Pickup';
}
