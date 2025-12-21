<?php
declare(strict_types=1);

class DeliveryDate{
  public $id;
  public $created;
  public $date;

  public static function create($date){
    require_once('sql.inc.php');
    $qry = 
      "INSERT INTO msl_delivery_dates ".
        "(`date`) VALUES ".
        "('" . sql_escape_string($date) . "')";
    $id = sql_insert($qry);
    $values = sql_select_one("SELECT * FROM msl_delivery_dates WHERE id=".intval($id));
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
    require_once('sql.inc.php');
    $qry = 
      "UPDATE msl_delivery_dates SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }

  public function delete(){
    require_once('sql.inc.php');
    $qry = 
      "DELETE FROM msl_delivery_dates ";
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }
}
