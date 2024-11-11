<?php
declare(strict_types=1);

class OrderItem{
  public int $id;
  public int $product_id;
  public float $amount_pieces;
  public float $amount_weight;
  public string $price_type;
  public float $price;
  public float $price_sum;

  public function update( array $updates = array() ){
    global $user;
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_order_items SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
