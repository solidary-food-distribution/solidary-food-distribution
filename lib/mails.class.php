<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('mail.class.php');

class Mails extends Objects{
  protected $_table = 'msl_mails';
  protected $_default_order_by = 'id';
  protected $_object_name = 'Mail';
}
