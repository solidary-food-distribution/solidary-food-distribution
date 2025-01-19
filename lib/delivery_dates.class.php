<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('delivery_date.class.php');

class DeliveryDates extends Objects{
  protected $_table = 'msl_delivery_dates';
  protected $_default_order_by = 'id';
  protected $_object_name = 'DeliveryDate';
}
