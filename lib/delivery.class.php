<?php
declare(strict_types=1);

require_once('delivery_item.class.php');
require_once('member.class.php');
require_once('user.class.php');

class Delivery{
  public $id;
  public Member $supplier;
  public $price_total;
  public DateTime $created;
  public User $creator;
  public array $items = array(); //class DeliveryItem

  public function item_create( $product_id ) {
    require_once('sql.class.php');
    $qry =
      "INSERT INTO msl_delivery_items ".
        "(delivery_id, product_id) VALUES ".
        "('" . intval($this->id) . "', '" . intval($product_id) . "')";
    $item_id = SQL::insert($qry);
    $item = new DeliveryItem();
    $item->id = $item_id;
    $item->product_id = intval($product_id);
    $items[$item->id] = $item;
    return $item;
  }

  public function item_delete( $item_id ) {
    require_once('sql.class.php');
    $qry =
      "DELETE FROM msl_delivery_items " .
        "WHERE delivery_id='" . intval($this->id) . "' AND id='" . intval($item_id) . "'";
    SQL::update($qry);
    return true;
  }
}
