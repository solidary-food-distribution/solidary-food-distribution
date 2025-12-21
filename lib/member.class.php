<?php

declare(strict_types=1);

class Member{
  public int $id;
  public string $created;
  public string $status;
  public string $deactivate_on;
  public string $name;
  public string $identification;
  public int $producer;
  public bool $consumer;
  public int $pate_id;
  public float $order_limit;
  public string $purchase_name;

  public function update( array $updates = array() ){
    require_once('sql.inc.php');
    $qry = 
      "UPDATE msl_members SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }

  public function set_session(){
    foreach(get_object_vars($this) as $key => $value){
      $_SESSION['member'][$key] = $value;
    }
  }

}
