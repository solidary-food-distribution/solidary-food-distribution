<?php
declare(strict_types=1);

require_once('task_user.class.php');

class Task{
  public $task_id;
  public $title;
  public $interval;
  public $effort;
  public $starts;
  public array $users = array(); //class TaskUser

  public function add_user( $user_id ) {
    require_once('sql.class.php');
    $qry =
      "INSERT INTO msl_task_users ".
        "(task_id, user_id) VALUES ".
        "('" . intval($this->task_id) . "', '" . intval($user_id) . "')";
    SQL::insert($qry);
    $task_user = new TaskUser();
    $task_user->task_id = intval($this->task_id);
    $task_user->user_id = intval($user_id);
    $this->users[$task_user->user_id] = $task_user;
    return $task_user;
  }

  public function remove_user( $user_id ) {
    require_once('sql.class.php');
    $qry =
      "DELETE FROM msl_task_users " .
        "WHERE task_id='" . intval($this->task_id) . "' AND user_id='" . intval($user_id) . "'";
    SQL::update($qry);
    return true;
  }

  public function update( array $updates = array() ){
    require_once('sql.class.php');
    $qry = 
      "UPDATE msl_tasks SET ";
    $qry .= SQL::buildUpdateQuery($updates).' ';
    $qry .= "WHERE task_id='".intval($this->task_id)."'";
    SQL::update($qry);
  }

  public function delete(){
    require_once('sql.class.php');
    $qry = 
      "DELETE FROM msl_tasks ";
    $qry .= "WHERE task_id='".intval($this->task_id)."'";
    SQL::update($qry);
  }
}