<?php
declare(strict_types=1);

require_once('_objects.class.php');
require_once('info_user.class.php');

class InfoUsers extends Objects{
  protected $_table = 'msl_info_users';
  protected $_id_key = 'info_id';
  protected $_default_order_by = 'info_id';
  protected $_object_name = 'InfoUser';
}
