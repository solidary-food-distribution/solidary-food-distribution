<?php

require_once('inc.php');
user_ensure_authed();
#user_needs_access('tasks');

function execute_index(){
  global $user;
  require_once('tasks.class.php');
  $tasks = new Tasks();
  return array('tasks'=>$tasks);
}


function execute_assign(){
  global $user;
  require_once('tasks.class.php');
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
  require_once('users.class.php');
  $users = new Users(array('id' => array_keys($user_ids)));
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
  
  require_once('tasks.class.php');
  $task = task_get($task_id);
  if(!isset($task->users[$user['user_id']])){
    $task->add_user( $user['user_id']);
  }
  $task->users[$user['user_id']]->update(array($field=>$value));

  echo json_encode(array('value' => $value));
  exit;
}
