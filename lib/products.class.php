<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('product.class.php');

class Products extends Objects{
  protected $_table = 'msl_products';
  protected $_default_order_by = 'name';
  protected $_object_name = 'Product';
}