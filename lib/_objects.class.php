<?php

class Objects extends ArrayIterator{
  private $array = array();

  public function __construct(array $filters=array(), array $orderby=array(), int $limit_start=0, int $limit_count=-1){
    $this->load_from_db($filters, $orderby, $limit_start, $limit_count);
    parent::__construct($this->array);
  }

  public function current(){
    return parent::current();
  }

  public function offsetGet($offset){
    return parent::offsetGet($offset);
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

  public function count(){
    return count($this->array);
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