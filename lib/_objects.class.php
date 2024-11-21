<?php

class Objects implements ArrayAccess,Iterator,Countable{
  private $array = array();
  private $valid = true;

  public function __construct(array $filters=array(), array $orderby=array(), int $limit_start=0, int $limit_count=-1){
    $this->load_from_db($filters, $orderby, $limit_start, $limit_count);
  }

  public function count(){
    return count($this->array);
  }
  public function current(){
    return current($this->array);
  }
  public function key(){
    return key($this->array);
  }
  public function next(){
    $next = next($this->array);
    if($next === false){
      $this->valid = false;
    }
    return $next;
  }
  public function rewind(){
    $this->valid = true;
    reset($this->array);
  }
  public function valid(){
    if(!count($this->array)){
      return false;
    }
    return $this->valid;
  }

  public function offsetExists(mixed $offset){
    return in_array($this->array, $offset);
  }
  public function offsetGet(mixed $offset){
    return $this->array[$offset];
  }
  public function offsetSet(mixed $offset, mixed $value){
    $this->array[$offset] = $value;
  }
  public function offsetUnset(mixed $offset){
    unset($this->array[$offset]);
  }

  public function array(){
    return $this->array;
  }
  public function isset($id){
    return isset($this->array[$id]);
  }
  public function get($id){
    if(isset($this->array[$id])){
      return $this->array[$id];
    }
    return null;
  }
  public function first(){
    if(!isset($this->array[key($this->array)])){
      return null;
    }
    return $this->array[key($this->array)];
  }
  public function keys(){
    return array_keys($this->array);
  }

  private function load_from_db(array $filters, array $orderby, int $limit_start, int $limit_count){
    require_once('sql.class.php');
    $id_key='';
    if(isset($this->_id_key)){
      $id_key=$this->_id_key.' AS id,';
    }
    $qry = "SELECT $id_key t.* FROM ".$this->_table." t WHERE 1=1 ";
    if(!empty($filters)){
      $qry .= "AND ".SQL::buildFilterQuery($filters);
    }
    if(!empty($orderby)){
      $qry .= "ORDER BY ".SQL::buildOrderbyQuery($orderby);
    }else{
      $qry .= "ORDER BY ".$this->_default_order_by;
    }
    $recset = SQL::selectID($qry, 'id');

    $this->array = array();
    foreach($recset as $id => $row){
      $object = new $this->_object_name();
      foreach($row as $key => $value){
        if(property_exists($object, $key)){
          $object->{$key} = $value;
        }
      }
      $this->array[$id] = $object;
    }
  }

}