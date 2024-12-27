<?php
declare(strict_types=1);

require_once('user.class.php');

class TaskUser{
  public int $task_id;
  public int $user_id;
  public int $assign; //percentage 0 50 100...
  public $start;
  public $end;
  public $comment;

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    if(isset($updates['assign'])){
      $updates['assign'] = intval($updates['assign']);
    }
    $qry = 
      "UPDATE msl_task_users SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE task_id='".intval($this->task_id)."' AND user_id='".intval($this->user_id)."'";
    SQL::update($qry);
  }
}
