<?php
declare(strict_types=1);

#require_once('task_user.class.php');

class Poll{
  public $poll_id;
  public $title;
  public $text;
  public $type;
  public $data;
  public $has_votes;

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_polls SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE poll_id='".intval($this->poll_id)."'";
    SQL::update($qry);
  }

}