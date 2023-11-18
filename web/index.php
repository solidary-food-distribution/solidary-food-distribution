<?php
$path=isset($_GET['index_path'])?$_GET['index_path']:'';
$path=strtolower($path);
$path=preg_replace('/[^a-z\/_]/', '', $path);
$path=explode('/', trim($path,'/'));
if(count($path)<2){
  $path[]='index';
}
if(empty($path[0])){
  $path[0]='start';
}
global $MODULE,$ACTION,$TEMPLATE,$LAYOUT,$PROPERTIES;
$MODULE=$path[0];
$ACTION=$path[1];
unset($path);
$PROPERTIES=array();

set_include_path('../lib/'.PATH_SEPARATOR.'../templates/');
session_start();

$action_script='../modules/'.$MODULE.'/action.php';
if(!file_exists($action_script)){
  die404();
}
require($action_script);
unset($action_script);

if(!function_exists('execute_'.$ACTION)){
  die404();
}
$callback_return=call_user_func('execute_'.$ACTION);

if(!isset($callback_return['template'])){
  $callback_return['template']=$ACTION.'.php';
}
$TEMPLATE=preg_replace('/[^a-z\._]/','',$callback_return['template']);
unset($callback_return['template']);

if(!isset($callback_return['layout'])){
  $callback_return['layout']='layout.php';
}
$LAYOUT=$callback_return['layout'];
unset($callback_return['layout']);

$CONTENT=render_template($callback_return);

$LAYOUT=preg_replace('/[^a-z\._]/','',$LAYOUT);
require('../templates/'.$LAYOUT);


function render_template(&$callback_return){
  global $MODULE,$ACTION,$TEMPLATE,$LAYOUT,$PROPERTIES;

  foreach($callback_return AS $callback_return_key=>$callback_return_value){
    ${$callback_return_key}=$callback_return_value;
  }
  unset($callback_return);
  unset($callback_return_key);
  unset($callback_return_value);

  ob_start();
  require('../modules/'.$MODULE.'/templates/'.$TEMPLATE);
  return ob_get_clean();
}

function die404(){
  http_response_code(404);
  die('This page was not found');
}