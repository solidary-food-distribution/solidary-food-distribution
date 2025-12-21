<?php
#declare(strict_types=1);

require_once('poll_answer.class.php');

class PollAnswers extends ArrayObject{

  public static function create($poll_id, $user_id, $answer){
    require_once('sql.inc.php');
    $qry = 
      "INSERT INTO msl_poll_answers ".
        "(poll_id, user_id, answer) VALUES ".
        "('" . intval($poll_id) . "', '" . intval($user_id) . "', '".sql_escape_string($answer)."')";
    $poll_answer_id = sql_insert($qry);
    return $poll_answer_id;
  }

  public function __construct(array $filters=array(), array $orderby=array(), int $limit_start=0, int $limit_count=-1){
    $array = $this->load_from_db($filters, $orderby, $limit_start, $limit_count);
    parent::__construct($array);
  }

  public function first(){
    $array = $this->getArrayCopy();
    return $array[key($array)];
  }

  public function keys(){
    return array_keys($this->getArrayCopy());
  }

  private function load_from_db(array $filters, array $orderby, int $limit_start, int $limit_count){
    require_once('sql.inc.php');
    $qry=
      "SELECT * ".
      "FROM msl_poll_answers ".
      "WHERE 1=1 ";
    if(!empty($filters)){
      $qry .= "AND ".sql_build_filter_query($filters);
    }
    if(!empty($orderby)){
      $qry .= "ORDER BY ".sql_build_orderby_query($orderby);
    }else{
      $qry .= "ORDER BY poll_answer_id";
    }
    if($limit_start > 0 || $limit_count >= 0){
      if($limit_count < 0){
        $limit_count = 9999;
      }
      $qry .=
        " LIMIT ".intval($limit_start).", ".intval($limit_count);
    }
    $rec = sql_select_id($qry,'poll_answer_id');
    $poll_answers = array();
    #logger(print_r($rec,1));
    foreach($rec as $poll_answer_id=>$pa){
      $poll_answer = new PollAnswer();
      $poll_answer->poll_answer_id = $pa['poll_answer_id'];
      $poll_answer->poll_id = $pa['poll_id'];
      $poll_answer->user_id = $pa['user_id'];
      $poll_answer->answer = $pa['answer'];
      $poll_answers[$poll_answer_id] = $poll_answer;
    }
    #logger(print_r($poll_answers,1));
    return $poll_answers;
  }

}


function poll_answer_get($poll_answer_id){
  $objects = new PollAnswers(array('poll_answer_id' => $poll_answer_id));
  if(!empty($objects)){
    return $objects->first();
  }
  return null;
}