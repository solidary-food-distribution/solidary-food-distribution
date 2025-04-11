<?php

declare(strict_types=1);

class Member{
  public int $id;
  public string $created;
  public string $name;
  public string $identification;
  public int $producer;
  public bool $consumer;

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_members SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    SQL::update($qry);
  }

}
