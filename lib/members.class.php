<?php
declare(strict_types=1);

require_once('member.class.php');

class Members extends ArrayObject{

  public function __construct(array $filters = array(), array $orderby = array(), int $limit_start = 0, int $limit_count = -1){
    $array = $this->load_from_db($filters, $orderby, $limit_start, $limit_count);
    parent::__construct($array);
  }

  public function first(){
    $array = $this->getArrayCopy();
    return $array[key($array)];
  }

  public function keys(){
    return array_keys($this->getArrayCopy());
  }

  private function load_from_db(array $filters, array $orderby, int $limit_start, int $limit_count){
    require_once('sql.class.php');
    $qry =
      "SELECT * ".
      "FROM msl_members m ";
    if(!empty($filters)){
      $qry .= "WHERE ".SQL::buildFilterQuery($filters);
    }
    if(!empty($orderby)){
      $qry .= "ORDER BY ".SQL::buildOrderbyQuery($orderby);
    }else{
      $qry .= "ORDER BY m.producer,m.consumer DESC,m.name";
    }
    $ms = SQL::selectID($qry, 'id');

    $members = array();
    foreach($ms as $id=>$m){
      $member = new Member();
      $member->id = intval($m['id']);
      $member->name = $m['name'];
      $member->identification = $m['identification'];
      $member->producer = boolval($m['producer']);
      $member->consumer = boolval($m['consumer']);
      $members[$id] = $member;
    }

    return $members;
  }

}