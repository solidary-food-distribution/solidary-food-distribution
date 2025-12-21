<?php
declare(strict_types=1);

require_once('member.class.php');

class Members extends ArrayObject{

  public static function create($name){
    require_once('sql.inc.php');
    $qry = 
      "INSERT INTO msl_members ".
        "(name) VALUES ".
        "('".sql_escape_string($name)."')";
    $member_id = sql_insert($qry);
    return $member_id;
  }

  public static function sget($id){
    $objects = new Members(array('id' => $id));
    if($objects->count()){
      return $objects->first();
    }
    return null;
  }

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
    require_once('sql.inc.php');
    $qry =
      "SELECT * ".
      "FROM msl_members m ";
    if(!empty($filters)){
      $qry .= "WHERE ".sql_build_filter_query($filters);
    }
    if(!empty($orderby)){
      $qry .= "ORDER BY ".sql_build_orderby_query($orderby);
    }else{
      $qry .= "ORDER BY m.producer,m.consumer DESC,m.name";
    }
    $ms = sql_select_id($qry, 'id');

    $members = array();
    foreach($ms as $id=>$m){
      $member = new Member();
      $member->id = intval($m['id']);
      $member->created = $m['created'];
      $member->name = $m['name'];
      $member->status = $m['status'];
      $member->deactivate_on = strval($m['deactivate_on']);
      $member->identification = $m['identification'];
      $member->producer = intval($m['producer']);
      $member->consumer = boolval($m['consumer']);
      $member->pate_id = intval($m['pate_id']);
      $member->order_limit = floatval($m['order_limit']);
      $member->purchase_name = strval($m['purchase_name']);
      $members[$id] = $member;
    }

    return $members;
  }

}

function member_get($id){
  $objects = new Members(array('id' => $id));
  if(!empty($objects)){
    return $objects->first();
  }
  return null;
}