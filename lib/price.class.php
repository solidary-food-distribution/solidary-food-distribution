<?php
declare(strict_types=1);

class Price{
  public int $product_id;
  public $start;
  public $end;
  public float $price;
  public float $price_bundle;
  public float $amount_per_bundle;
  public float $tax;
  public float $purchase;

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_prices SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

}
