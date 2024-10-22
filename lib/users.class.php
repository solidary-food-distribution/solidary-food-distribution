<?php
declare(strict_types=1);

require_once('user.class.php');

class Users extends ArrayObject{

  public static function create($email, $name, $member_id=0){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_users ".
        "(email, name, member_id, passwd, passwd_tmp, pickup_pin) VALUES ".
        "('".SQL::escapeString($email)."', '".SQL::escapeString($name)."', ".intval($member_id).", '', '', '')";
    $user_id = SQL::insert($qry);
    return $user_id;
  }

  public function __construct(array $filters = array(), array $orderby = array(), int $limit_start = 0, int $limit_count = -1){
    $users = $this->load_from_db($filters, $orderby, $limit_start, $limit_count);
    parent::__construct($users);
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
      "FROM msl_users u ";
    if(!empty($filters)){
      $qry .= "WHERE ".SQL::buildFilterQuery($filters);
    }
    if(!empty($orderby)){
      $qry .= "ORDER BY ".SQL::buildOrderbyQuery($orderby);
    }
    $us = SQL::selectID($qry, 'id');

    $users = array();
    foreach($us as $id=>$u){
      $user = new User();
      $user->id = $u['id'];
      $user->name = $u['name'];
      $user->email = $u['email'];
      $user->password = $u['passwd'];
      $user->member_id = $u['member_id'];
      $users[$id] = $user;
    }

    $users = $this->load_access_from_db($users);

    return $users;
  }

  private function load_access_from_db($users){
    if(empty($users)){
      return array();
    }
    $keys = array_keys($users);
    require_once('sql.class.php');
    $qry =
      "SELECT a.user_id,a.access,a.start,a.end,a.member_id, m.name ".
      "FROM msl_access a ".
        "LEFT JOIN msl_members m ON (a.member_id = m.id) ".
      "WHERE start<=CURDATE() AND end>=CURDATE() ".
        "AND user_id IN (".SQL::escapeArray($keys).") ".
      "ORDER BY (CASE WHEN a.member_id=0 THEN 0 ELSE 1 END),m.name,a.access";

    $as = SQL::select($qry);
    foreach($as as $a){
      $users[$a['user_id']]->access[$a['access']][$a['member_id']]['start'] = $a['start'];
      $users[$a['user_id']]->access[$a['access']][$a['member_id']]['end'] = $a['end'];
      if($a['member_id']){
        $users[$a['user_id']]->members[$a['member_id']][$a['access']]['start'] = $a['start'];
        $users[$a['user_id']]->members[$a['member_id']][$a['access']]['end'] = $a['end'];
      }
    }
    foreach($users as $id=>$user){
      if(isset($user->access['products']) 
        || isset($user->access['members'])
        || isset($user->access['users'])
        || isset($user->access['orders'])
        || isset($user->access['debits'])){
        $user->access['admin']=1;
      }
    }
    return $users;
  }
}

function user_get($id){
  $objects = new Users(array('id' => $id));
  if(!empty($objects)){
    return $objects->first();
  }
  return null;
}