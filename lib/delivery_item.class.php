<?php
declare(strict_types=0);

class DeliveryItem{
  public int $id;
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



  public function update( array $updates = array() ){
    global $user;
    $updates['modified'] = date('Y-m-d H:i:s');
    $updates['modifier_id'] = $user['user_id'];
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_delivery_items SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
