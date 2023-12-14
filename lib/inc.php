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
      unset($request['index_path']); //not needed
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
    return '?';
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

function format_money($value){
  $return = number_format(floatval($value),2,',','');
  if($return === '0,00'){
    $return = '&nbsp;';
  }
  return $return;
}

function format_amount($value){
  $return = str_replace('.', ',', round($value,3));
  if($return === '0'){
    $return = '&nbsp;';
  }
  return $return;
}

function format_weight($value){
  $return = number_format(floatval($value),3,',','');
  if($return == '0,000'){
    $return = '&nbsp;';
  }elseif(substr($return, -4) == ',000'){
    $return = number_format(floatval($value),1,',','');
  }elseif(substr($return, -1) == '0'){
    $return = number_format(floatval($value),2,',','');
  }elseif(substr($return, -2) == '00'){
    $return = number_format(floatval($value),1,',','');
  }
  return $return;
}

function format_date($date, $format='j.n.Y', $weekday=true){
  if(gettype($date)=='object' && get_class($date)=='DateTime'){
    $time=$date->getTimestamp();
  }else{
    $time=strtotime($date);
  }
  $ret = date($format,$time);
  if($weekday){
    $weekdays=array('Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag');
    $weekday=date('w',$time);
    $weekday=$weekdays[$weekday];
    $ret = substr($weekday,0,2).'., '.$ret;
  }
  return $ret;
}

function send_email($to, $subject, $text){
  $header='From: "Mit Sinn Leben eG" <buchen@mit-sinn-leben.de>'."\r\n".
    'Reply-To: buchen@mit-sinn-leben.de'."\r\n".
    'Content-Type: text/plain;charset=UTF-8'."\r\n".
    'X-Mailer: PHP/' . phpversion();
  logger("send_email $to $subject");
  mail($to, $subject, $text, $header);
}

function html_input($data){
  $return = '<div class="input';
  if(isset($data['class'])){
    $return .= ' '.$data['class'];
  }
  if(isset($data['type'])){
    $return .= ' '.$data['type'];
  }
  $return .= '"';
  if(isset($data['type'])){
    $return .= ' data-type="'.$data['type'].'"';
  }
  if(isset($data['info'])){
    $return .= ' data-info="'.htmlentities($data['info']).'"';
  }
  if(isset($data['url'])){
    $return .= ' data-url="'.$data['url'].'"';
  }
  if(isset($data['field'])){
    $return .= ' data-field="'.$data['field'].'" onclick="input_onfocus(this)"';
  }
  $return .= '>';
  if(isset($data['options'])){
    foreach($data['options'] as $value => $label){
      $id = $data['field'].'_'.$value;
      $return .= '<div class="option"><input type="radio" name="'.$data['field'].'" id="'.$id.'" value="'.$value.'"';
      if($data['value'] == $value){
        $return .= ' checked="checked"';
      }
      $return .= ' /><label for="'.$id.'">'.htmlentities($label).'</label></div>';
    }
  }elseif(isset($data['value'])){
    $return .= $data['value'];
  }
  $return .= '</div>';
  return $return;
}