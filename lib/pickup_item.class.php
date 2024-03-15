<?php
declare(strict_types=1);

require_once('product.class.php');

class PickupItem{
  public int $id;
  public int $delivery_item_id;
  public Product $product;
  public float $amount_pieces;
  public float $amount_weight;
  public string $price_type;
  public float $price;
  public float $price_sum;
  public DateTime $best_before;

  public function update( array $updates = array() ){
    global $user;
    $updates['modified'] = date('Y-m-d H:i:s');
    $updates['modifier_id'] = $user['user_id'];
    if($updates['price'] == ''){
      $updates['price'] = '0';
    }
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_pickup_items SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
