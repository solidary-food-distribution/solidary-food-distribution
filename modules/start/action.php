<?php

require_once('inc.php');
user_ensure_authed();

function execute_index(){
  global $user;
  $message = '';
  $deactivated = 0;
  require_once('members.class.php');
  $member = Members::sget($user['member_id']);
  if(!user_has_access('order') && $member->status != 'a'){
    $message = 'Der Bestellzugriff ist deaktiviert.';
    if($member->pate_id){
      $message .= ' Die Patenschaft ist am '.format_date($member->deactivate_on).' ausgelaufen.';
    }
    $deactivated = 1;
  }elseif($member->pate_id){
    $message = 'Dies ist ein Patenschaft-Zugang, er gilt bis '.format_date($member->deactivate_on);
  }
  if($deactivated == 0 && isset($_SESSION['scale']) && $_SESSION['scale']){
    if(user_has_access('pickups') && !user_has_access('deliveries') && !user_has_access('inventory')){
      forward_to_page('/pickups');
    }else{
      forward_to_page('/start/store');
    }
  }
  /* replaced with forum
  require_once('infos.class.php');
  $infos = new Infos(array('published!=' => '0000-00-00 00:00:00', 'published>' => date('Y-m-d', strtotime('-1 months', time()))));
  require_once('info_users.class.php');
  $info_users = new InfoUsers(array('user_id' => $user['user_id'], 'read!=' => '0000-00-00 00:00:00'));
  foreach($info_users as $info_user){
    if(isset($infos[$info_user->info_id])){
      unset($infos[$info_user->info_id]);
    }
  }
  */
  $forum_posts = array();
  if(!isset($_SESSION['scale']) && !isset($_SESSION['start_forum_read'])){
    require_once('sql.inc.php');
    $qry = "SELECT t.forum_id, f.name AS forum_name, p.topic_id, t.name AS topic_name, MAX(p.created) AS max_created, MIN(p.id) AS min_post_id, COUNT(p.id) AS count_posts FROM msl_forum_posts p, msl_forum_topics t, msl_forums f
  WHERE p.topic_id=t.id AND t.forum_id=f.id AND p.created_by!='".intval($user['user_id'])."' AND p.created>='".sql_escape_string($_SESSION['last_login'])."' GROUP BY t.forum_id, f.name, p.topic_id, t.name ORDER BY f.sort, max_created DESC;";
    $forum_posts = sql_select_id2($qry, 'forum_id', 'topic_id');
  }

  return array(/*'infos' => $infos,*/ 'message' => $message, 'forum_posts' => $forum_posts, 'deactivated' => $deactivated);
}

function execute_forum_read_ajax(){
  global $user;
  $_SESSION['start_forum_read']=1;
  echo json_encode(array('result'=>1));
  exit;
}

/*
function execute_info_read_ajax(){
  global $user;
  $info_id = get_request_param('info_id');
  require_once('info_users.class.php');
  $info_users = new InfoUsers(array('info_id' => $info_id, 'user_id' => $user['user_id']));
  if(!$info_users->count()){
    $info_user = InfoUser::create($info_id, $user['user_id']);
  }else{
    $info_user = $info_users->first();
  }
  $info_user->update(array('read' => date('Y-m-d H:i:s')));
  echo json_encode(array('result'=>1));
  exit;
}
*/

function execute_store(){
}

function execute_version(){
}

function execute_noaccess(){
}