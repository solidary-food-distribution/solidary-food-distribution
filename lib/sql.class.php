<?php
global $mysqli;
if(strpos($_SERVER['HTTP_HOST'],'.local')){
  $mysqli=mysqli_connect('mysqlsrv','root','deepinform','msl_buchen');
}else{
  $mysqli=mysqli_connect('***PROD_DB_HOST***','***PROD_DB_USER***','***PROD_DB_PWD***','***PROD_DB_DATABASE***');
}
if(mysqli_connect_errno()){
    die('Fehler: '.mysqli_connect_errno());
}else{
  mysqli_set_charset($mysqli,"UTF8");
}
class SQL{
  static function update($qry){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    if(!$res){
      self::log($qry);
    }
  }
  static function insert($qry){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    if(!$res){
      self::log($qry);
      return false;
    }
    $ret=mysqli_insert_id($mysqli);
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
      self::log($qry);
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
      self::log($qry);
    }
  }
  static function selectKey2Val($qry,$key,$val){
    global $mysqli;
    $res=mysqli_query($mysqli,$qry);
    if(empty($res)){
      self::log($qry);
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
      self::log($qry);
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
      self::log($qry);
    }
    $ret=array();
    while ($row = mysqli_fetch_assoc($res)) {
      $ret[$row[$id]][$row[$id2]]=$row;
    }
    return $ret;
  }
  static function aToString($ar){
    $str="";
    foreach($ar as $v){
      $str .=$v.',';
    }
    return rtrim($str,',');
  }
  static function aToStr2($ar){
    $str="";
    foreach($ar as $v){
      $str .="'".$v."',";
    }
    return rtrim($str,',');
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
    return preg_replace('/[^\w-]/', '', $str);
  }
  static function log($qry){
    global $mysqli, $MODULE, $ACTION;
    file_put_contents(__DIR__.'/../log/sql_error.log',date('Y-m-d H:i:s')." $MODULE $ACTION\n".mysqli_error($mysqli)." >> $qry\n",FILE_APPEND);
  }
}
