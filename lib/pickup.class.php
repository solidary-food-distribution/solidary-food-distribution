<?php
declare(strict_types=1);

class Pickup{
  public $id;
  public $member_id;
  public $user_id;
  public $created;
  public $price_total;
  public $status;

  public static function create( $member_id, $user_id){
    require_once('sql.class.php');
    $qry = "INSERT INTO msl_pickups (member_id, user_id) VALUES (".intval($member_id).", ".intval($user_id).")";
    $id = SQL::insert($qry);
    if(!$id){
      return false;
    }
    $values = SQL::selectOne("SELECT * FROM msl_pickups WHERE id=".intval($id));
    $pu = new Pickup();
    $pu->_init_values($values); 
    return $pu;
  }

  public function _init_values( $values ){
    foreach($values as $key => $value){
      if(property_exists($this, $key)){
        $this->{$key} = $value;
      }
    }
  }

  /*
  public function item_create( $product_id, $delivery_item_id ) {
    require_once('sql.class.php');
    $qry =
      "INSERT INTO msl_pickup_items ".
        "(pickup_id, product_id, delivery_item_id) VALUES ".
        "('" . intval($this->id) . "', '" . intval($product_id) . "', '" . intval($delivery_item_id) . "')";
    $item_id = SQL::insert($qry);
    $item = new PickupItem();
    $item->id = $item_id;
    $item->product_id = intval($product_id);
    $item->delivery_item_id = intval($delivery_item_id);
    $items[$item->id] = $item;
    return $item;
  }

  public function item_delete( $item_id ) {
    require_once('sql.class.php');
    $qry =
      "DELETE FROM msl_pickup_items " .
        "WHERE pickup_id='" . intval($this->id) . "' AND id='" . intval($item_id) . "'";
    SQL::update($qry);
    return true;
  }
  */

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
