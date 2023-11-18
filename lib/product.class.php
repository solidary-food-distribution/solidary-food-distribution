<?php


class Product{
  public $id;
  public $name;
  public $producer_id;
  public $producer_name;
  public $type;
  public $period;
  public $amount_steps;
  public $amount_min;
  public $amount_max;
  public $status;
  public $orders_lock_date;
  public $price;
  public $tax;
  public $tax_incl;

  public function __construct($product_id,$init_by_database=true){
    $this->id=$product_id;
  }

  public function init_with_array($array){
    unset($array['id']);
    foreach($array as $k=>$v){
      if(property_exists('Product',$k)){
        $this->{$k}=$v;
      }
    }
  }

}
