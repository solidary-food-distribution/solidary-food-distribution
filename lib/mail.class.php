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
    require_once('sql.class.php');
    $qry = "INSERT INTO msl_mails (`to`, subject, content) VALUES ('".SQL::escapeString($to)."', '".SQL::escapeString($subject)."', '".SQL::escapeString($content)."')";
    $id = SQL::insert($qry);
    if(!$id){
      return false;
    }
    $values = SQL::selectOne("SELECT * FROM msl_mails WHERE id=".intval($id));
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
    require_once('sql.class.php');
    $qry = "UPDATE msl_mails SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

  public function delete() {
    require_once('sql.class.php');
    $qry =
      "DELETE FROM msl_mails " .
        "WHERE id='" . intval($this->id) . "'";
    SQL::update($qry);
    return true;
  }
}
