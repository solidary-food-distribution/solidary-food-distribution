<?php
declare(strict_types=1);

class Purchase{
  public $id;
  public $created;
  public $delivery_date_id;
  public $supplier_id;
  public $datetime;
  public $status;
  public $sent;

  public static function create( $supplier_id, $datetime){
    require_once('sql.class.php');
    $qry = "INSERT INTO msl_purchases (supplier_id, `datetime`) VALUES (".intval($supplier_id).", '".SQL::escapeString($datetime)."')";
    $id = SQL::insert($qry);
    if(!$id){
      return false;
    }
    $values = SQL::selectOne("SELECT * FROM msl_purchases WHERE id=".intval($id));
    $object = new Purchase();
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
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_purchases SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

  public function delete(){
    require_once('sql.class.php');
    $qry = 
      "DELETE FROM msl_purchases ";
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
