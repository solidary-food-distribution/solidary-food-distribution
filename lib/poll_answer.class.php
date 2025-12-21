<?php
declare(strict_types=1);

class PollAnswer{
  public $poll_answer_id;
  public $poll_id;
  public $user_id;
  public $answer;

  public function update( array $updates = array() ){
    require_once('sql.inc.php');
    $qry = 
      "UPDATE msl_poll_answers SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE poll_answer_id='".intval($this->poll_answer_id)."'";
    sql_update($qry);
  }

}
