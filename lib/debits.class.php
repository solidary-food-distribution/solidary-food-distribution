<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('debit.class.php');

class Debits extends Objects{
  protected $_table = 'msl_debits';
  protected $_default_order_by = 'id';
  protected $_object_name = 'Debit';
}
