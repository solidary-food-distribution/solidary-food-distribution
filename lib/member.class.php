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
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_members SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

}
