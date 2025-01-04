<?php

require_once('inc.php');
user_ensure_authed();
#user_needs_access('tasks');

require_once('tasks.class.php');

function execute_index(){
  global $user;
  #require_once('tasks.class.php');
  #$tasks = new Tasks();
  #return array('tasks'=>$tasks);
}


function execute_calendar(){
  $month = get_request_param('month');
  if($month==''){
    $month = date('Y-m',time());
  }
  $month_time = strtotime($month.'-01');
  $month_prev = date('Y-m',strtotime("-1 months", $month_time));
  $month_next = date('Y-m',strtotime("+1 months", $month_time));

  $months=array();
  $m = date('Y-m-d',strtotime("-3 months", $month_time));
  while(count($months) < 7){
    $months[substr($m,0,7)] = translate_month(intval(substr($m, 5, 2)))." ".substr($m, 0, 4);
    $m = date('Y-m-d',strtotime("+1 months", strtotime($m)));
  }

  $wd = date('w', $month_time)-1;
  if($wd<0){
    $wd+=7;
  }
  $start = date('Y-m-d',strtotime("-$wd days", $month_time));
  $end=date('Y-m-d',strtotime("+1 month", $month_time));
  $wde = date('w',strtotime($end));
  $wde = 7 - $wde;
  if($wde >= 6){
    $wde="-".(7-$wde);
  }else{
    $wde="+".$wde;
  }
  $wde+=1;
  $end=date('Y-m-d',strtotime("$wde days", strtotime($end)));

  $ts = new Tasks(array('type' => 'e', 'starts>=' => $start, 'starts<=' => $end), array('starts' => 'ASC'));
  $tasks = array();
  foreach($ts as $t){
    $tasks[substr($t->starts, 0, 10)][] = $t;
  }
  
  return array('month'=>$month, 'months'=>$months, 'month_prev'=>$month_prev, 'month_next'=>$month_next, 'start'=>$start, 'end'=>$end, 'tasks'=>$tasks);
}

function execute_day(){
  global $user;
  $day = get_request_param('day');
  if($day == ''){
    $day = date('Y-m-d',time());
  }

  $day_prev = '';
  $ts = new Tasks(array('type' => 'e', 'starts<' => $day), array('starts' => 'DESC'), 0, 1);
  if(!empty($ts)){
    $day_prev = substr($ts->first()->starts, 0, 10);
  }
  $day_next = '';
  $day_plus_one = date('Y-m-d',strtotime("+1 days", strtotime($day)));
  $ts = new Tasks(array('type' => 'e', 'starts>=' => $day_plus_one), array('starts' => 'ASC'), 0, 1);
  if(!empty($ts)){
    $day_next = substr($ts->first()->starts, 0, 10);
  }

  $tasks = new Tasks(array('type' => 'e', 'starts>=' => $day, 'starts<' => $day_plus_one), array('starts' => 'ASC', 'title' => 'ASC'));
  $task_users = array();
  $user_ids = array();
  foreach($tasks as $task){
    foreach($task->users as $tu){
      if($tu->user_id == $user['user_id'] || empty($task_users[$task->task_id])){
        $task_users[$task->task_id][$tu->user_id] = $tu;
      }else{
        $task_users[$task->task_id] = array($tu->user_id => $tu) + $task_users[$task->task_id];
      }
      $user_ids[] = $tu->user_id;
    }
  }
  #logger(print_r($task_users,1));

  require_once('users.class.php');
  $users = new Users(array('id' => $user_ids));

  return array('day' => $day, 'day_prev' => $day_prev, 'day_next' => $day_next, 'tasks' => $tasks, 'task_users' => $task_users, 'users' => $users);
}


function execute_assign(){
  global $user;
  $tasks = new Tasks(array('type'=>'a'), array('sort'=>'ASC'));
  $user_ids = array();
  foreach($tasks as &$task){
    $sorted=array();
    foreach($task->users as &$task_user){
      if($task_user->user_id==$user['user_id']){
        array_unshift($sorted,$task_user);
      }else{
        $sorted[]=$task_user;
      }
      $user_ids[$task_user->user_id]=1;
    }
    $task->users=$sorted;
  }
  #logger(print_r($tasks,1));
  $users=array();
  if(!empty($user_ids)){
    require_once('users.class.php');
    $users = new Users(array('id' => array_keys($user_ids)));
  }
  #logger(print_r($users,1));
  return array('tasks'=>$tasks, 'users'=>$users);
}


function execute_assign_update_ajax(){
  global $user;
  $task_id = get_request_param('task_id');
  $field = get_request_param('field');
  $type = get_request_param('type');
  $value = get_request_param('value');
  
  logger($user['user_id']." $task_id $field $type $value");
  if(!in_array($field,array('assign','comment'))){
    exit;
  }

  $task = task_get($task_id);
  if(!isset($task->users[$user['user_id']])){
    $task->add_user( $user['user_id']);
  }
  $task->users[$user['user_id']]->update(array($field=>$value));

  echo json_encode(array('value' => $value));
  exit;
}
