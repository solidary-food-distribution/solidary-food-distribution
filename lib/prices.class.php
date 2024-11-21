<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('price.class.php');

class Prices extends Objects{
  protected $_table = 'msl_prices';
  protected $_id_key = 'product_id';
  protected $_default_order_by = 'product_id';
  protected $_object_name = 'Price';
}