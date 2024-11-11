<?php
#declare(strict_types=1);

require_once('_objects.class.php');
require_once('poll_vote.class.php');

class PollVotes extends Objects{
  protected $_table = 'msl_poll_votes';
  protected $_default_order_by = 'poll_answer_id';
  protected $_object_name = 'PollVote';
  protected $_id_key = 'CONCAT(poll_answer_id,\'_\',user_id)';

  public static function create($poll_answer_id, $user_id, $value){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_poll_votes ".
        "(poll_answer_id, user_id, value, created, updated) VALUES ".
        "('".intval($poll_answer_id)."', '".intval($user_id)."', '".SQL::escapeString($value)."', NOW(), NOW())";
    SQL::insert($qry);
  }
}