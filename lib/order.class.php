<?php
declare(strict_types=1);

class Order{
  public int $id;
  public int $member_id;
  public string $pickup_date; //REFACTOR DateTime

  public static function create($member_id, $pickup_date){
    require_once('sql.inc.php');
    $qry="INSERT INTO msl_orders (member_id, pickup_date) VALUES (".intval($member_id).", '".sql_escape_string($pickup_date)."')";
    $id = sql_insert($qry);
    if(!$id){
      return false;
    }
    $o = new Order();
    $o->id = intval($id);
    $o->member_id = intval($member_id);
    $o->pickup_date = $pickup_date;
    return $o;
  }
}
