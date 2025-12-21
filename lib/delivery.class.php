<?php
declare(strict_types=1);

class Delivery{
  public $id;
  public $supplier_id; //member->id
  public $purchase_total;
  public $created;
  public $creator_id; //user->id

  public static function create($supplier_id, $creator_id){
    require_once('sql.inc.php');
    $qry = 
      "INSERT INTO msl_deliveries ".
        "(supplier_id, creator_id, created) VALUES ".
        "('" . intval($supplier_id) . "', '" . intval($creator_id) . "', NOW())";
    $id = sql_insert($qry);
    if(!$id){
      return false;
    }
    $values = sql_select_one("SELECT * FROM msl_deliveries WHERE id=".intval($id));
    $object = new Delivery();
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
      "UPDATE msl_deliveries SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }

  public function delete(){
    require_once('sql.inc.php');
    $qry = 
      "DELETE FROM msl_deliveries ";
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }
}
