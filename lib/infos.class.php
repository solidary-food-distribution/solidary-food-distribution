<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('info.class.php');

class Infos extends Objects{
  protected $_table = 'msl_infos';
  protected $_default_order_by = 'id';
  protected $_object_name = 'Info';
}
