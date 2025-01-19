<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('purchase.class.php');

class Purchases extends Objects{
  protected $_table = 'msl_purchases';
  protected $_default_order_by = '`datetime`';
  protected $_object_name = 'Purchase';
}