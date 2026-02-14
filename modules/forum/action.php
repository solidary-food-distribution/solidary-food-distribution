<?php

require_once('inc.php');
require_once('sql.inc.php');
user_ensure_authed();

function execute_index(){
}
function execute_index_ajax(){
  global $user;
  $forums = sql_select("SELECT *, (SELECT t.id FROM msl_forum_topics t WHERE t.forum_id=f.id ORDER BY last_post_id DESC LIMIT 1) topic_id  FROM msl_forums f ORDER BY sort");
  $topic_ids = array();
  foreach($forums as $forum){
    $topic_ids[] = $forum['topic_id'];
  }
  $topics = sql_select_id("SELECT t.forum_id, t.name AS latest_topic, DATE_FORMAT(p.created,'%d.%m.%Y %H:%i') AS latest_date FROM msl_forum_topics t LEFT JOIN msl_forum_posts p ON (t.last_post_id=p.id) WHERE t.id IN (".sql_escape_array($topic_ids).")", 'forum_id');
  foreach($forums as $key => $forum){
    $forums[$key]['latest_topic'] = $topics[$forum['id']]['latest_topic'];
    $forums[$key]['latest_date'] = $topics[$forum['id']]['latest_date'];
  }
  
  $return = array('forums' => $forums);
  echo json_encode($return);
  exit;
}

function execute_forum(){
  $id = get_request_param('id');
  $return = array();
  if(!forum_user_post_user_id("forum", $id)){
    $return['hide_topic_new'] = 1;
  }
  return $return;
}
function execute_forum_ajax(){
  $id = get_request_param('id');
  $forum = sql_select_one("SELECT id AS forum_id, name AS forum_name FROM msl_forums WHERE id='".intval($id)."'");
  $topics = sql_select("SELECT t.*,p.id AS latest_id,CONCAT(SUBSTR(p.text,1,50),'...') AS latest_text, DATE_FORMAT(p.created,'%d.%m.%Y %H:%i') AS latest_date FROM msl_forum_topics t LEFT JOIN msl_forum_posts p ON (t.last_post_id = p.id) WHERE t.forum_id='".intval($id)."' ORDER BY (CASE WHEN pinned>0 THEN pinned ELSE 999 END),t.last_post_id DESC");
  
  $return = array(
    'forum' => $forum,
    'topics' => $topics,
  );
  echo json_encode($return);
  exit;
}

function execute_topic(){
}
function execute_topic_ajax(){
  global $user;
  $id = get_request_param('id');
  $topic = sql_select_one("SELECT t.id AS topic_id, t.name AS topic_name, t.created_by, t.forum_id, f.name AS forum_name FROM msl_forum_topics t, msl_forums f WHERE f.id=t.forum_id AND t.id='".intval($id)."'");
  $posts = sql_select("SELECT p.id, p.text, DATE_FORMAT(p.created,'%d.%m.%Y %H:%i') AS created, u.name AS created_by_name, IF(p.created_by='".intval($user['user_id'])."',1,0) AS post_editable FROM msl_forum_posts p LEFT JOIN msl_users u ON (p.created_by = u.id) WHERE p.topic_id='".intval($id)."' ORDER BY p.id ASC");
  
  $topic['editable'] = ($topic['created_by'] == $user['user_id'])?'1':'0';

  $return = array(
    'topic' => $topic,
    'posts' => $posts,
  );
  echo json_encode($return);
  exit;
}

function execute_topic_new(){
  $forum_id = get_request_param('forum_id');
  if(!forum_user_post_user_id("forum", $forum_id)){
    exit;
  }
}
function execute_topic_new_ajax(){
  global $user;
  $forum_id = get_request_param('forum_id');
  $forum = sql_select_one("SELECT id AS forum_id, name AS forum_name FROM msl_forums WHERE id='".intval($forum_id)."'");

  $return = array(
    'forum' => $forum,
  );
  echo json_encode($return);
  exit;
}
function execute_topic_new_post_ajax(){
  $forum_id = get_request_param('forum_id');
  $topic_name = trim(get_request_param('topic_name'));
  $post_text = trim(get_request_param('post_text'));

  $user_id = forum_user_post_user_id("forum", $forum_id);
  if(!$user_id){
    exit;
  }
  $error = '';
  if(empty($topic_name)){
    $error .= 'Bitte Thema angeben. ';
  }
  if(empty($post_text)){
    $error .= 'Bitte Inhalt angeben. ';
  }
  if(empty($error)){
    $topic_id = sql_select_one("SELECT id FROM msl_forum_topics WHERE forum_id='".intval($forum_id)."' AND name='".sql_escape_string($topic_name)."'")['topic_id'];
    if(empty($topic_id)){
      $topic_id = sql_insert("INSERT INTO msl_forum_topics (forum_id, name) VALUES ('".intval($forum_id)."', '".sql_escape_string($topic_name)."')");
    }
    $post_id = sql_insert("INSERT INTO msl_forum_posts (topic_id, `text`, created_by) VALUES ('".intval($topic_id)."', '".sql_escape_string($post_text)."', '".intval($user_id)."')");
    sql_update("UPDATE msl_forum_topics SET last_post_id='".intval($post_id)."' WHERE id='".intval($topic_id)."'");
  }

  $return = array(
    'error' => $error,
    'topic_id' => $topic_id,
    'post_id' => $post_id
  );
  echo json_encode($return);
  exit;
}

function execute_topic_edit(){
}
function execute_topic_edit_ajax(){
  global $user;
  $id = get_request_param('id');
  $topic = sql_select_one("SELECT t.id AS topic_id, t.name AS topic_name, t.created_by, t.forum_id, f.name AS forum_name FROM msl_forum_topics t, msl_forums f WHERE f.id=t.forum_id AND t.id='".intval($id)."'");

  if($topic['created_by'] != $user['user_id']){
    exit;
  }

  $return = array(
    'topic' => $topic,
  );
  echo json_encode($return);
  exit;
}
function execute_topic_edit_post_ajax(){
  global $user;
  $id = get_request_param('id');
  $topic_name = trim(get_request_param('topic_name'));

  $error = '';
  if(empty($topic_name)){
    $error .= 'Bitte Thema angeben. ';
  }
  if(empty($error)){
    $topic = sql_select_one("SELECT * FROM msl_forum_topics WHERE id='".intval($id)."'");
    logdata("topic_id $id ".$topic['name']);
    sql_update("UPDATE msl_forum_topics SET name='".sql_escape_string($topic_name)."' WHERE id='".intval($id)."' AND created_by='".$user['user_id']."'");
  }

  $return = array(
    'error' => $error,
    'id' => $id
  );
  echo json_encode($return);
  exit;
}

function execute_post_new(){
}
function execute_post_new_ajax(){
  global $user;
  $topic_id = get_request_param('topic_id');
  $topic = sql_select_one("SELECT t.id AS topic_id, t.name AS topic_name, t.forum_id, f.name AS forum_name FROM msl_forum_topics t, msl_forums f WHERE f.id=t.forum_id AND t.id='".intval($topic_id)."'");
  $return = array(
    'topic' => $topic,
  );
  echo json_encode($return);
  exit;
}
function execute_post_new_post_ajax(){
  global $user;
  $topic_id = get_request_param('topic_id');
  $post_text = trim(get_request_param('post_text'));

  $topic = sql_select_one("SELECT t.id AS topic_id, t.name AS topic_name, t.forum_id, f.name AS forum_name FROM msl_forum_topics t, msl_forums f WHERE f.id=t.forum_id AND t.id='".intval($topic_id)."'");
  $user_id = $user['user_id'];
  $error = '';
  if(empty($post_text)){
    $error .= 'Bitte Inhalt angeben. ';
  }
  if(empty($error)){
    $post_id = sql_insert("INSERT INTO msl_forum_posts (topic_id, `text`, created_by) VALUES ('".intval($topic_id)."', '".sql_escape_string($post_text)."', '".intval($user_id)."')");
    sql_update("UPDATE msl_forum_topics SET last_post_id='".intval($post_id)."' WHERE id='".intval($topic_id)."'");
  }
  $return = array(
    'error' => $error,
    'topic_id' => $topic_id,
    'post_id' => $post_id
  );
  echo json_encode($return);
  exit;
}

function execute_post_edit(){
}
function execute_post_edit_ajax(){
  global $user;
  $id = get_request_param('id');
  $post = sql_select_one("SELECT p.*,t.id AS topic_id, t.name AS topic_name, t.forum_id, f.name AS forum_name FROM msl_forum_posts p, msl_forum_topics t, msl_forums f WHERE f.id=t.forum_id AND t.id=p.topic_id AND p.id='".intval($id)."'");

  if($post['created_by'] != $user['user_id']){
    exit;
  }

  $return = array(
    'post' => $post,
  );
  echo json_encode($return);
  exit;
}
function execute_post_edit_post_ajax(){
  global $user;
  $id = get_request_param('id');
  $post_text = trim(get_request_param('post_text'));

  $error = '';
  if(empty($post_text)){
    $error .= 'Bitte Inhalt angeben. ';
  }
  $post = sql_select_one("SELECT topic_id, `text` FROM msl_forum_posts WHERE id='".intval($id)."'");
  if(empty($error) && $post['text']!==$post_text){
    logdata("post_id $id ".$post['text']);
    sql_update("UPDATE msl_forum_posts SET `text`='".sql_escape_string($post_text)."',modified=NOW() WHERE id='".intval($id)."' AND created_by='".intval($user['user_id'])."'");
  }

  $return = array(
    'error' => $error,
    'topic_id' => $post['topic_id'],
  );
  echo json_encode($return);
  exit;
}

function forum_user_post_user_id($context, $context_id){
  global $user;
  if($context == 'forum' && $context_id < 3 && !in_array($user['user_id'], array(4,2))){
    return 0;
  }
  return $user['user_id'];
}