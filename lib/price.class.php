<?php
declare(strict_types=0);

class Price{
  public int $product_id;
  public $start;
  public $end;
  public float $price;
  public float $price_bundle;
  public float $amount_per_bundle;
  public float $tax;
  public float $purchase;
  public float $suggested_retail;

  public static function create($product_id){
    require_once('sql.inc.php');
    $qry = "INSERT INTO msl_prices (`product_id`, `start`, `end`, tax, purchase) VALUES (".intval($product_id).", 
      CURDATE(), '9999-12-31', 0, 0)";
    sql_insert($qry);
    $values = sql_select_one("SELECT * FROM msl_prices WHERE product_id=".intval($product_id));
    $object = new Price();
    $object->_init_values($values);
    return $object;
  }

  public function _init_values( $values ){
    foreach($values as $key => $value){
      if(property_exists($this, $key)){
        $this->{$key} = $value;
      }
    }
  }

  public function update( array $updates = array() ){
    require_once('sql.inc.php');
    $qry = 
      "UPDATE msl_prices SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE product_id='".intval($this->product_id)."' AND `start`='".sql_escape_string($this->start)."' AND `end`='".sql_escape_string($this->end)."'";
    sql_update($qry);
  }

}
