<?php
declare(strict_types=1);

class DeliveryDate{
  public $id;
  public $created;
  public $date;

  public static function create($date){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_delivery_dates ".
        "(`date`) VALUES ".
        "('" . SQL::escapeString($date) . "')";
    $id = SQL::insert($qry);
    $values = SQL::selectOne("SELECT * FROM msl_delivery_dates WHERE id=".intval($id));
    $object = new DeliveryDate();
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
      "UPDATE msl_delivery_dates SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

  public function delete(){
    require_once('sql.class.php');
    $qry = 
      "DELETE FROM msl_delivery_dates ";
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }
}
