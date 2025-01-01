<?php
declare(strict_types=1);

class Delivery{
  public $id;
  public $supplier_id; //member->id
  public $purchase_total;
  public $created;
  public $creator_id; //user->id

  public static function create($supplier_id, $creator_id){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_deliveries ".
        "(supplier_id, creator_id, created) VALUES ".
        "('" . intval($supplier_id) . "', '" . intval($creator_id) . "', NOW())";
    $delivery_id = SQL::insert($qry);
    return $delivery_id;
  }

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_deliveries SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

  public function delete(){
    require_once('sql.class.php');
    $qry = 
      "DELETE FROM msl_deliveries ";
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
