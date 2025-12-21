<?php
declare(strict_types=0);

class Mail{
  public int $id;
  public string $created;
  public string $to;
  public string $subject;
  public string $content;
  public string $sent;

  public static function create($to, $subject, $content){
    require_once('sql.inc.php');
    $qry = "INSERT INTO msl_mails (`to`, subject, content) VALUES ('".sql_escape_string($to)."', '".sql_escape_string($subject)."', '".sql_escape_string($content)."')";
    $id = sql_insert($qry);
    if(!$id){
      return false;
    }
    $values = sql_select_one("SELECT * FROM msl_mails WHERE id=".intval($id));
    $object = new Mail();
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
    $qry = "UPDATE msl_mails SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }

  public function delete() {
    require_once('sql.inc.php');
    $qry =
      "DELETE FROM msl_mails " .
        "WHERE id='" . intval($this->id) . "'";
    sql_update($qry);
    return true;
  }
}
