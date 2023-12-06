<?php

declare(strict_types=1);

require_once('member.class.php');

class Product{
  public int $id;
  public string $name;
  public Member $producer;
  public string $type;
  public string $period;
  public float $amount_steps;
  public float $amount_min;
  public float $amount_max;
  public string $status;
  public string $orders_lock_date; //REFACTOR DateTime
  public float $price;
  public float $tax;
  public bool $tax_incl;

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_products SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE pid='".intval($this->id)."'";
    SQL::update($qry);
  }

}

function product_get($id){
  require_once('products.class.php');
  $products = new Products(array('product_id' => $id));
  if(!empty($products)){
    return $products->first();
  }
  return null;
}