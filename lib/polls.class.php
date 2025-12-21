<?php
#declare(strict_types=1);

require_once('poll.class.php');

class Polls extends ArrayObject{

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
      "FROM msl_polls ".
      "WHERE 1=1 ";
    if(!empty($filters)){
      $qry .= "AND ".sql_build_filter_query($filters);
    }
    if(!empty($orderby)){
      $qry .= "ORDER BY ".sql_build_orderby_query($orderby);
    }else{
      $qry .= "ORDER BY poll_id";
    }
    if($limit_start > 0 || $limit_count >= 0){
      if($limit_count < 0){
        $limit_count = 9999;
      }
      $qry .=
        " LIMIT ".intval($limit_start).", ".intval($limit_count);
    }
    $rec = sql_select_id($qry,'poll_id');
    $polls = array();
    #logger(print_r($rec,1));
    foreach($rec as $poll_id=>$p){
      $poll = new Poll();
      $poll->poll_id = $p['poll_id'];
      $poll->title = $p['title'];
      $poll->text = $p['text'];
      $poll->type = $p['type'];
      $poll->data = $p['data'];
      $poll->has_votes = $p['has_votes'];
      $poll->close_datetime = $p['close_datetime'];
      $polls[$poll_id] = $poll;
    }
    #logger(print_r($polls,1));
    return $polls;
  }

}


function poll_get($poll_id){
  $objects = new Polls(array('poll_id' => $poll_id));
  if(!empty($objects)){
    return $objects->first();
  }
  return null;
}