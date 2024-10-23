<?php
global $mysqli;
if(strpos($_SERVER['HTTP_HOST'],'.local')){
  $mysqli=mysqli_connect('mysqlsrv','root','***LOCAL_DB_PWD***','msl_buchen');
}else{
  $mysqli=mysqli_connect('***PROD_DB_HOST***','***PROD_DB_USER***','***PROD_DB_PWD***','***PROD_DB_DATABASE***');
}
if(mysqli_connect_errno()){
    die('Fehler: '.mysqli_connect_errno());
}else{
  mysqli_set_charset($mysqli,"UTF8");
}
mysqli_report(MYSQLI_REPORT_ERROR);

class SQL{
  static function update($qry){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    self::log_info($qry,$res);
    if(!$res){
      self::log_error($qry);
      return false;
    }
    
    return self::affected_rows();
  }
  static function insert($qry){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    if(!$res){
      self::log_error($qry);
      return false;
    }
    $ret=mysqli_insert_id($mysqli);
    self::log_info($qry,$ret);
    return $ret;
  }
  static function affected_rows(){
    global $mysqli;
    return mysqli_affected_rows($mysqli);
  }
  static function select($qry){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    if(empty($res)){
      self::log_error($qry);
    }
    $ret=array();
    while ($row = mysqli_fetch_assoc($res)) {
      $ret[]=$row;
    }
    return $ret;
  }
  static function selectOne($qry){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    $row = mysqli_fetch_assoc($res);
    $ret[]=$row;
    if(!empty($res)){
      return $ret[0];
    }else{
      self::log_error($qry);
    }
  }
  static function selectKey2Val($qry,$key,$val){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    if(empty($res)){
      self::log_error($qry);
    }
    $ret=array();
    while ($row = mysqli_fetch_assoc($res)) {
      $ret[$row[$key]]=$row[$val];
    }
    return $ret;
  }

  static function selectID($qry,$id){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    if(empty($res)){
      self::log_error($qry);
    }
    $ret=array();
    while ($row = mysqli_fetch_assoc($res)) {
      $ret[$row[$id]]=$row;
    }
    return $ret;
  }
  static function selectID2($qry,$id,$id2){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    if(empty($res)){
      self::log_error($qry);
    }
    $ret=array();
    while ($row = mysqli_fetch_assoc($res)) {
      $ret[$row[$id]][$row[$id2]]=$row;
    }
    return $ret;
  }
  static function escapeArray(&$ar){
    $str="";
    foreach($ar as $v){
      $str.="'".SQL::escapeString($v)."',";
    }
    return rtrim($str,',');
  }
  static function escapeString($str){
    global $mysqli;
    return $mysqli->real_escape_string($str);
  }
  static function escapeFieldName($str){
    //nur A-Z0-9 und _
    return preg_replace('/[^\w\-\.]/', '', $str);
  }
  static function log_info($qry,$info){
    global $mysqli, $MODULE, $ACTION, $user;
    file_put_contents(__DIR__.'/../log/sql_info.'.date('Ymd').'.log',date('Y-m-d H:i:s')." $MODULE $ACTION ".$user['user_id']."\n$qry\n$info\n",FILE_APPEND);
  }
  static function log_error($qry){
    global $mysqli, $MODULE, $ACTION, $user;
    file_put_contents(__DIR__.'/../log/sql_error.log',date('Y-m-d H:i:s')." $MODULE $ACTION ".$user['user_id']."\n".mysqli_error($mysqli)." >> $qry\n",FILE_APPEND);
  }

  static function buildFilterQuery($filters){
    $qry='';
    foreach($filters as $field => $value){
      $qry .= ' AND '.SQL::escapeFieldName($field);
      if(is_array($value)){
        $qry .= ' IN ('.SQL::escapeArray($value).') ';
      }else{
        if(strpos($value, '%') !== false){
          $qry .= " LIKE ";
        }elseif(substr($field, -2, 2) == '<=' || substr($field, -2, 2) == '>=' || substr($field, -2, 2) == '!='){
          $qry .= substr($field, -2, 2);
        }elseif(substr($field, -1, 1) == '<' || substr($field, -1, 1) == '>'){
          $qry .= substr($field, -1, 1);
        }else{
          $qry .= " = ";
        }
        $qry .= "'".SQL::escapeString($value)."' ";
      }
    }
    return substr($qry, 5);
  }
  static function buildOrderbyQuery($orderby){
    $qry='';
    foreach($orderby as $field => $dir){
      $qry .= ', '.SQL::escapeFieldName($field).' '.SQL::escapeFieldName($dir);
    }
    return ltrim($qry, ',');
  }
  static function buildUpdateQuery($updates){
    $qry='';
    foreach($updates as $field => $value){
      $qry .= ', '.SQL::escapeFieldName($field)." = '".SQL::escapeString($value)."'";
    }
    return ltrim($qry, ',');
  }
}
