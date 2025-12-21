<?php
declare(strict_types=0);

class Info{
  public $id;
  public $created;
  public $subject;
  public $content;
  public $published;

  public static function create(){
    require_once('sql.inc.php');
    $qry = "INSERT INTO msl_infos (created) VALUES (NOW())";
    $id = sql_insert($qry);
    if(!$id){
      return false;
    }
    $values = sql_select_one("SELECT * FROM msl_infos WHERE id=".intval($id));
    $info = new Info();
    $info->_init_values($values); 
    return $info;
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
    $qry = "UPDATE msl_infos SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }
}