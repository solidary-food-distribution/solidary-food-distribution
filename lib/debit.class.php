<?php
declare(strict_types=1);

class Debit{
  public $id;
  public $created;
  public $member_id;
  public $pickup_id;
  public $amount;
  public $due_date;
  public $status;
  public $exported;

  public static function create($member_id){
    require_once('sql.class.php');
    $qry = "INSERT INTO msl_debits (member_id) VALUES (".intval($member_id).")";
    $id = SQL::insert($qry);
    if(!$id){
      return false;
    }
    $values = SQL::selectOne("SELECT * FROM msl_debits WHERE id=".intval($id));
    $d = new Debit();
    $d->_init_values($values);
    return $d;
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
      "UPDATE msl_debits SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

}
