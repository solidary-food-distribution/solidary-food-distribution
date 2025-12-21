<?php
declare(strict_types=1);

class PollVote{
  public $poll_answer_id;
  public $user_id;
  public $value;

  public function update( array $updates = array() ){
    require_once('sql.inc.php');
    $qry = 
      "UPDATE msl_poll_votes SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE poll_answer_id='".intval($this->poll_answer_id)."' AND user_id='".intval($this->user_id)."'";
    sql_update($qry);
  }

}
