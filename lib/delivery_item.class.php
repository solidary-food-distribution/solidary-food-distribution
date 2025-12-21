<?php
declare(strict_types=0);

class DeliveryItem{
  public int $id;
  public string $modified;
  public int $delivery_id;
  public int $product_id;
  public int $amount_pieces;
  public int $amount_bundles;
  public float $amount_weight;
  public string $price_type;
  public float $purchase;
  public float $purchase_sum;
  public float $dividable;
  public string $best_before;
  public float $weight_min;
  public float $weight_max;
  public float $weight_avg;


  public static function create($delivery_id, $product_id){
    require_once('sql.inc.php');
    $qry = 
      "INSERT INTO msl_delivery_items ".
        "(delivery_id, product_id, created, modified) VALUES ".
        "('" . intval($delivery_id) . "', '" . intval($product_id) . "', NOW(), NOW())";
    $id = sql_insert($qry);
    $values = sql_select_one("SELECT * FROM msl_delivery_items WHERE id=".intval($id));
    $delivery_item = new DeliveryItem();
    $delivery_item->_init_values($values); 
    return $delivery_item;
  }

  public function _init_values( $values ){
    foreach($values as $key => $value){
      if(property_exists($this, $key)){
        $this->{$key} = $value;
      }
    }
  }

  public function delete(){
    require_once('sql.inc.php');
    $qry = "DELETE FROM msl_delivery_items WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }

  public function update( array $updates = array() ){
    global $user;
    $updates['modified'] = date('Y-m-d H:i:s');
    $updates['modifier_id'] = $user['user_id'];
    require_once('sql.inc.php');
    $qry = 
      "UPDATE msl_delivery_items SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }
}
