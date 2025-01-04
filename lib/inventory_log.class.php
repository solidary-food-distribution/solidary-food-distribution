<?php
declare(strict_types=0);

require_once('product.class.php');

class InventoryLog{
  public int $id;
  public string $created;
  public string $modified;
  public int $product_id;
  public int $delivery_item_id;
  public int $pickup_item_id;
  public int $user_id;
  public float $amount_pieces;
  public float $amount_weight;
  public float $dividable;
  public float $weight_min;
  public float $weight_max;
  public float $weight_avg;

  public static function create($inventory_id, $product_id, $delivery_item_id, $pickup_item_id, $user_id){
    require_once('sql.class.php');
    $qry = "INSERT INTO msl_inventory_log ($id, product_id, delivery_item_id, pickup_item_id, user_id) VALUES (".intval($inventory_id).",".intval($product_id).",".intval($delivery_item_id).",".intval($pickup_item_id).",".intval($user_id).")";
    $id = SQL::insert($qry);
    if(!$id){
      return false;
    }
    $values = SQL::selectOne("SELECT * FROM msl_inventory WHERE id=".intval($id));
    $inventory = new InventoryLog();
    $inventory->_init_values($values); 
    return $inventory;
  }

  public function _init_values( $values ){
    foreach($values as $key => $value){
      if(property_exists($this, $key)){
        $this->{$key} = $value;
      }
    }
  }

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    $qry = "UPDATE msl_inventory SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
