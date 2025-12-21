<?php
//declare(strict_types=1);

class PickupItem{
  public int $id;
  public string $modified;
  public int $pickup_id;
  public int $product_id;
  public int $order_item_id;
  public int $delivery_item_id;
  public float $amount_pieces_min;
  public float $amount_pieces_max;
  public float $amount_pieces;
  public float $amount_weight_min;
  public float $amount_weight_max;
  public float $amount_weight;
  public string $price_type;
  public float $price;
  public float $amount_per_bundle;
  public float $price_bundle;
  public float $price_sum;

  public static function create($pickup_id, $product_id){
    require_once('sql.inc.php');
    $qry="INSERT INTO msl_pickup_items (pickup_id, product_id) VALUES (".intval($pickup_id).", ".intval($product_id).")";
    $id = sql_insert($qry);
    if(!$id){
      return false;
    }
    $values = sql_select_one("SELECT * FROM msl_pickup_items WHERE id=".intval($id));
    $pui = new PickupItem();
    $pui->_init_values($values); 
    return $pui;
  }

  public function _init_values( $values ){
    foreach($values as $key => $value){
      if(property_exists($this, $key)){
        $this->{$key} = $value;
      }
    }
  }

  public function update( array $updates = array() ){
    global $user;
    $updates['modified'] = date('Y-m-d H:i:s');
    $updates['modifier_id'] = $user['user_id'];
    require_once('sql.inc.php');
    $qry = 
      "UPDATE msl_pickup_items SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }
}
