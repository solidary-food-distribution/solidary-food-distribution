<?php
global $mysqli;
if(!isset($_SERVER['HTTP_HOST']) || strpos($_SERVER['HTTP_HOST'],'.local')){
  $env_file = '../config/database.local.env';
}else{
  $env_file = '../config/database.env';
}
$env = parse_ini_file($env_file);
$mysqli=mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PWD'], $env['DB_DATABASE']);
if(mysqli_connect_errno()){
    die('Fehler: '.mysqli_connect_errno());
}else{
  mysqli_set_charset($mysqli,"UTF8");
}
mysqli_report(MYSQLI_REPORT_ERROR);

function sql_update($qry){
  global $mysqli;
  $res = mysqli_query($mysqli, $qry);
  sql_log_info($qry,$res);
  if(!$res){
    sql_log_error($qry);
    return false;
  }
  return sql_affected_rows();
}

function sql_insert($qry){
  global $mysqli;
  $res = mysqli_query($mysqli, $qry);
  if(!$res){
    sql_log_error($qry);
    return false;
  }
  $ret = mysqli_insert_id($mysqli);
  sql_log_info($qry, $ret);
  return $ret;
}

function sql_affected_rows(){
  global $mysqli;
  return mysqli_affected_rows($mysqli);
}

function sql_select($qry){
  global $mysqli;
  $res = mysqli_query($mysqli, $qry);
  if(empty($res)){
    sql_log_error($qry);
  }
  $ret = array();
  while($row = mysqli_fetch_assoc($res)){
    $ret[]=$row;
  }
  return $ret;
}
function sql_select_one($qry){
  global $mysqli;
  $res = mysqli_query($mysqli, $qry);
  if(empty($res)){
    sql_log_error($qry);
    return false;
  }
  $row = mysqli_fetch_assoc($res);
  $ret[]=$row;
  if(!empty($res)){
    return $ret[0];
  }
}
function sql_select_key2value($qry, $key, $val){
  global $mysqli;
  $res = mysqli_query($mysqli, $qry);
  if(empty($res)){
    sql_log_error($qry);
  }
  $ret = array();
  while($row = mysqli_fetch_assoc($res)) {
    $ret[$row[$key]] = $row[$val];
  }
  return $ret;
}

function sql_select_id($qry, $id){
  global $mysqli;
  $res = mysqli_query($mysqli, $qry);
  if(empty($res)){
    sql_log_error($qry);
  }
  $ret=array();
  while($row = mysqli_fetch_assoc($res)){
    $ret[$row[$id]] = $row;
  }
  return $ret;
}

function sql_select_id2($qry, $id, $id2){
  global $mysqli;
  $res = mysqli_query($mysqli, $qry);
  if(empty($res)){
    sql_log_error($qry);
  }
  $ret = array();
  while($row = mysqli_fetch_assoc($res)){
    $ret[$row[$id]][$row[$id2]] = $row;
  }
  return $ret;
}

function sql_escape_array($ar){
  $str = "";
  foreach($ar as $v){
    $str .= "'".sql_escape_string($v)."',";
  }
  return rtrim($str,',');
}

function sql_escape_string($str){
  global $mysqli;
  return $mysqli->real_escape_string($str);
}

function sql_escape_fieldname($str){
  //nur A-Z0-9 und _
  return preg_replace('/[^\w\-\.]/', '', $str);
}

function sql_log_info($qry, $info){
  global $mysqli, $MODULE, $ACTION, $user;
  file_put_contents(__DIR__.'/../log/sql_info.'.date('Ymd').'.log', date('Y-m-d H:i:s')." $MODULE $ACTION ".(isset($user['user_id'])?$user['user_id']:0)."\n$qry\n$info\n",FILE_APPEND);
}

function sql_log_error($qry){
  global $mysqli, $MODULE, $ACTION, $user;
  file_put_contents(__DIR__.'/../log/sql_error.log', date('Y-m-d H:i:s')." $MODULE $ACTION ".(isset($user['user_id'])?$user['user_id']:0)."\n".mysqli_error($mysqli)." >> $qry\n",FILE_APPEND);
}

function sql_build_filter_query($filters){
  $qry='';
  foreach($filters as $field => $value){
    if(strpos($field, '.')){
      $tmp = explode('.', $field);
      $field_escaped = '`'.sql_escape_fieldname($tmp[0]).'`.`'.sql_escape_fieldname($tmp[1]).'`';
    }else{
      $field_escaped = '`'.sql_escape_fieldname($field).'`';
    }
    if(is_array($value)){
      if(empty($value)){
        $qry .= ' AND 1=2 ';
      }else{
        $qry .= ' AND '.$field_escaped.' '.(substr($field, -2, 2) == '!='?'NOT':'').' IN ('.sql_escape_array($value).') ';
      }
    }else{
      $qry .= ' AND '.$field_escaped;
      if(strpos($value, '%') !== false){
        $qry .= " LIKE ";
      }elseif(substr($field, -2, 2) == '<=' || substr($field, -2, 2) == '>=' || substr($field, -2, 2) == '!='){
        $qry .= substr($field, -2, 2);
      }elseif(substr($field, -1, 1) == '<' || substr($field, -1, 1) == '>'){
        $qry .= substr($field, -1, 1);
      }else{
        $qry .= " = ";
      }
      $qry .= "'".sql_escape_string($value)."' ";
    }
  }
  return substr($qry, 5);
}

function sql_build_orderby_query($orderby){
  $qry='';
  foreach($orderby as $field => $dir){
    #TODO FIELD(feld,werte...)
    $qry .= ', '.$field.' '.sql_escape_fieldname($dir);
    #$qry .= ', '.sql_escape_fieldname($field).' '.sql_escape_fieldname($dir);
  }
  return ltrim($qry, ',');
}

function sql_build_update_query($updates){
  $qry='';
  foreach($updates as $field => $value){
    $qry .= ', `'.sql_escape_fieldname($field)."` = '".sql_escape_string($value)."'";
  }
  return ltrim($qry, ',');
}

