<?php
declare(strict_types=1);

class OrderItem{
  public int $id;
  public int $order_id;
  public int $product_id;
  public float $amount_pieces;
  public float $amount_weight;
  public string $price_type;
  public float $price;
  public float $price_sum;

  public static function create($order_id, $product_id){
    require_once('sql.class.php');
    $qry="INSERT INTO msl_order_items (order_id, product_id) VALUES (".intval($order_id).", ".intval($product_id).")";
    $id = SQL::insert($qry);
    if(!$id){
      return false;
    }
    $oi = new OrderItem();
    $oi->id = intval($id);
    $oi->order_id = intval($order_id);
    $oi->product_id = intval($product_id);
    $oi->amount_pieces = 0;
    $oi->amount_weight = 0;
    $oi->price_type = '';
    $oi->price = 0;
    $oi->price_sum = 0;
    return $oi;
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
