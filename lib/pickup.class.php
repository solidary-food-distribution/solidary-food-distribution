<?php
declare(strict_types=1);

require_once('pickup_item.class.php');
require_once('member.class.php');
require_once('user.class.php');

class Pickup{
  public $id;
  public Member $member;
  public User $user;
  public DateTime $created;
  public $price_total;
  public $status;
  public array $items = array(); //class DeliveryItem

  public function item_create( $product_id ) {
    require_once('sql.class.php');
    $qry =
      "INSERT INTO msl_pickup_items ".
        "(pickup_id, product_id) VALUES ".
        "('" . intval($this->id) . "', '" . intval($product_id) . "')";
    $item_id = SQL::insert($qry);
    $item = new PickupItem();
    $item->id = $item_id;
    $item->product_id = intval($product_id);
    $items[$item->id] = $item;
    return $item;
  }

  public function item_delete( $item_id ) {
    require_once('sql.class.php');
    $qry =
      "DELETE FROM msl_pickup_items " .
        "WHERE delivery_id='" . intval($this->id) . "' AND id='" . intval($item_id) . "'";
    SQL::update($qry);
    return true;
  }

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_pickups SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

  public function delete(){
    require_once('sql.class.php');
    $qry = 
      "DELETE FROM msl_pickups ";
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
