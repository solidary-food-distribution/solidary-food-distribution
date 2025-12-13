<?php
declare(strict_types=0);

class OrderItem{
  public int $id;
  public string $updated;
  public int $replaces_id;
  public int $order_id;
  public int $product_id;
  public float $amount_pieces;
  public float $amount_max;
  public float $amount_weight;
  public string $split_status;
  public string $split_data;
  public string $price_type;
  public float $price;
  public float $amount_per_bundle;
  public float $price_bundle;
  public float $price_sum;
  public string $comment;

  public static function create($order_id, $product_id){
    require_once('sql.class.php');
    $qry="INSERT INTO msl_order_items (order_id, product_id) VALUES (".intval($order_id).", ".intval($product_id).")";
    $id = SQL::insert($qry);
    if(!$id){
      return false;
    }
    $values = SQL::selectOne("SELECT * FROM msl_order_items WHERE id=".intval($id));
    $oi = new OrderItem();
    $oi->_init_values($values); 
    return $oi;
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
    $qry = 
      "UPDATE msl_order_items SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

  public function delete(){
    require_once('sql.class.php');
    $qry = 
      "DELETE FROM msl_order_items ";
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
