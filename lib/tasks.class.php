<?php
#declare(strict_types=1);

require_once('task.class.php');

class Tasks extends ArrayObject{

  public static function create($starts, $title){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_tasks ".
        "(starts, title) VALUES ".
        "('" . SQL::escapeString($starts) . "', '" . SQL::escapeString($title) . "')";
    $task_id = SQL::insert($qry);
    return $task_id;
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
    if(isset($filters['task_id'])){
      $filters['t.task_id'] = $filters['task_id'];
      unset($filters['task_id']);
    }
    require_once('sql.class.php');
    $qry=
      "SELECT t.task_id, t.title, t.description, t.starts, t.interval, t.effort, ".
        "tu.user_id, tu.assign, tu.start tu_start, tu.end tu_end, tu.comment tu_comment ".
      "FROM msl_tasks t ".
        "LEFT JOIN msl_task_users tu ON (t.task_id = tu.task_id) ".
       "WHERE 1=1 ";
    if(!empty($filters)){
      $qry .= "AND ".SQL::buildFilterQuery($filters);
    }
    if(!empty($orderby)){
      $qry .= "ORDER BY ".SQL::buildOrderbyQuery($orderby);
    }else{
      $qry .= "ORDER BY t.starts, t.task_id";
    }
    if($limit_start > 0 || $limit_count >= 0){
      if($limit_count < 0){
        $limit_count = 9999;
      }
      $qry .=
        " LIMIT ".intval($limit_start).", ".intval($limit_count);
    }
    $ts = SQL::selectID2($qry,'task_id','user_id');
    $tasks = array();
    #logger(print_r($ts,1));
    foreach($ts as $task_id=>$tus){
      $data = $tus[key($tus)];
      $task = new Task();
      $task->task_id = $data['task_id'];
      $task->title = $data['title'];
      $task->description = $data['description'];
      $task->interval = $data['interval'];
      $task->effort = $data['effort'];
      $task->starts = $data['starts'];
      foreach($tus AS $user_id => $tu){
        if((string)$user_id===''){
          continue;
        }
        $task_user = new TaskUser();
        $task_user->task_id = intval($task_id);
        $task_user->user_id = intval($user_id);
        $task_user->assign = intval($tu['assign']);
        $task_user->start = $tu['tu_start'];
        $task_user->end = $tu['tu_end'];
        $task_user->comment = $tu['tu_comment'];
        $task->users[$user_id] = $task_user;
      }
      $tasks[$task_id] = $task;
    }
    #logger(print_r($tasks,1));
    return $tasks;
  }

}


function task_get($task_id){
  $objects = new Tasks(array('task_id' => $task_id));
  if(!empty($objects)){
    return $objects->first();
  }
  return null;
}