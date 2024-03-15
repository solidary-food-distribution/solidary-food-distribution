<?php
declare(strict_types=1);

require_once('product.class.php');

class Inventory{
  public int $id;
  public int $delivery_item_id;
  public int $pickup_item_id;
  public Product $product;
  public int $amount_pieces;
  public float $amount_weight;
  public float $dividable;
  public float $weight_min;
  public float $weight_max;
  public float $weight_avg;


  public function update( array $updates = array() ){
    $updates['modified'] = date('Y-m-d H:i:s');
    require_once('sql.class.php');
    $qry = "UPDATE msl_inventory SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
