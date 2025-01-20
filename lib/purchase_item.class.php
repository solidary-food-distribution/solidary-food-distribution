<?php
declare(strict_types=0);

class PurchaseItem{
  public int $id;
  public string $modified;
  public int $purchase_id;
  public int $product_id;
  public int $amount_pieces;
  public int $amount_bundles;
  public float $amount_weight;
  public string $price_type;
  public float $purchase;
  public float $purchase_sum;

  public static function create($purchase_id, $product_id){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_purchase_items ".
        "(purchase_id, product_id, created, modified) VALUES ".
        "('" . intval($purchase_id) . "', '" . intval($product_id) . "', NOW(), NOW())";
    $id = SQL::insert($qry);
    $values = SQL::selectOne("SELECT * FROM msl_purchase_items WHERE id='".intval($id)."'");
    $object = new PurchaseItem();
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

  public function delete(){
    require_once('sql.class.php');
    $qry = "DELETE FROM msl_purchase_items WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

  public function update( array $updates = array() ){
    global $user;
    $updates['modified'] = date('Y-m-d H:i:s');
    $updates['modifier_id'] = $user['user_id'];
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_purchase_items SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
