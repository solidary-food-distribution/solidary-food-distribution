<?php
global $user;

function logger($str){
  global $MODULE,$ACTION;
  file_put_contents(__DIR__.'/../log/logger.log',date('Y-m-d H:i:s')." $MODULE $ACTION\n$str\n",FILE_APPEND);
}

function get_request_param($param){
  if(isset($_REQUEST[$param])){
    return $_REQUEST[$param];
  }
  return '';
}
function set_request_param($param,$value){
  $_REQUEST[$param]=$value;
}

function user_ensure_authed(){
  global $user,$MODULE,$ACTION;
  if(!isset($_SESSION['user'])){
    $forward='/'.$MODULE.'/'.$ACTION;
    if($forward=='/auth/login' || $forward=='/start/index'){
      $forward='';
    }
    if(!empty($forward)){
      $forward='_forward='.$forward;
    }
    if(!empty($_REQUEST)){
      $request=$_REQUEST;
      unset($request['password']); //never expose password
      $forward.='&_query='.urlencode(http_build_query($request));
    }
    if(!empty($forward)){
      $forward='?'.ltrim($forward,'&');
    }
    header('Location: /auth/login'.$forward, true, 302);
    exit;
  }
  $user=$_SESSION['user'];
}

function user_has_access($access,$member=''){
  return isset($_SESSION['user']['access'][$access]);
}

function user_needs_access($access,$member=''){
  if(!user_has_access($access,$member)){
    forward_to_noaccess();
  }
}

function forward_to_noaccess(){
  header('Location: /start/noaccess');
  exit;
}

function forward_to_page($forward,$query=''){
  $location='/';
  if(!empty($forward)){
    $location=$forward;
  }
  if(!empty($query)){
    $location.='?'.$query;
  }
  header('Location: '.$location, true, 302);
  exit;
}

function translate_product_type($type){
  $translate=array(
    'p'=>'Stück',
    'k'=>'kg',
    'b'=>'EUR',
  );
  if(!isset($translate[$type])){
    return $type;
  }
  return $translate[$type];
}

function translate_product_period($period){
  $translate=array(
    'm'=>'Monat',
    'w'=>'Woche',
  );
  if(!isset($translate[$period])){
    return $period;
  }
  return $translate[$period];
}

function translate_access($access){
  $translate=array(
    'access'=>'Zugriff verwalten',
    'order'=>'Abholmengen eingeben',
    'pickups'=>'Abholung durchführen',
    'preferences'=>'Präferenzen auswählen',
    'deliveries'=>'Lieferung erfassen',
    'products'=>'Produkte verwalten',
    'members'=>'Mitglieder verwalten',
    'users'=>'Benutzer verwalten',
    'orders'=>'Abholmengen verwalten',
    'debits'=>'Abbuchungen verwalten',
  );
  if(!isset($translate[$access])){
    return $access;
  }
  return $translate[$access];
}

function translate_access_start($start){
  if($start!=='0000-00-00'){
    return 'vom '.$start;
  }
  return '';
}

function translate_access_end($end){
  if($end!=='9999-12-31'){
    return 'bis inkl '.$end;
  }
  return '';
}

function translate_month($month){
  $months=array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
  return $months[intval($month)-1];
}

function format_date($date){
  $weekdays=array('Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag');
  $weekday=date('w',strtotime($date));
  $weekday=$weekdays[$weekday];
  return substr($weekday,0,2).'., '.date('j.n.Y',strtotime($date));
}

function send_email($to,$subject,$text){
  $header='From: buchen@mit-sinn-leben.de'."\r\n".
    'Reply-To: buchen@mit-sinn-leben.de'."\r\n".
    'Content-Type: text/plain;charset=UTF-8'."\r\n".
    'X-Mailer: PHP/' . phpversion();
  mail($to,$subject,$text,$header);
}