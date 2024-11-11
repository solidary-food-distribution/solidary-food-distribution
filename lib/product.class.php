<?php
declare(strict_types=1);

class Product{
  public int $id;
  public string $name;
  public int $supplier_id;
  public string $type;
  public string $period;
  public float $amount_steps;
  public float $amount_min;
  public float $amount_max;
  public string $status;
  public int $pieces_per_package;
  public string $supplier_product_id;
  public string $gtin_piece;
  public string $gtin_package;

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_products SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

}
