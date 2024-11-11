<?php

require_once('inc.php');
user_ensure_authed();
#user_needs_access('polls');

function execute_index(){
  global $user;
  require_once('polls.class.php');
  $polls = new Polls();
  return array('polls' => $polls);
}

function execute_poll(){
  global $user;
  $poll_id = get_request_param('poll_id');
  require_once('polls.class.php');
  $poll = poll_get($poll_id);
  if(!$poll){
    exit;
  }
  require_once('poll_answers.class.php');
  $poll_answers = new PollAnswers(array('poll_id' => $poll_id), array('answer' => 'ASC'));
  if($poll->type == 'm' && $poll->has_votes && count($poll_answers)){
    $answers = array();
    $user_answers = array();
    foreach($poll_answers as $poll_answer){
      if($poll_answer->user_id == $user['user_id']){
        $user_answers[$poll_answer->poll_answer_id] = $poll_answer;
      }else{
        $answers[$poll_answer->poll_answer_id] = $poll_answer;
      }
    }
    #logger(print_r($user_answers,1));
    $poll_answers = $answers + $user_answers;
    #logger(print_r($poll_answers,1));
    $votes = array();
    $user_votes = array();
    require_once('poll_votes.class.php');
    $poll_votes = new PollVotes(array('poll_answer_id' => array_keys($poll_answers), 'value' => '1'));
    logger(print_r($poll_votes,1));
    foreach($poll_votes as $poll_vote){
      $votes[$poll_vote->poll_answer_id] = $votes[$poll_vote->poll_answer_id] + intval($poll_vote->value);
      if($poll_vote->user_id == $user['user_id'] && $poll_vote->value){
        $user_votes[$poll_vote->poll_answer_id] = $poll_vote->value;
      }
    }
    logger(print_r($votes,1));
    logger(print_r($user_votes,1));
  }
  return array('poll' => $poll, 'poll_answers' => $poll_answers, 'votes' => $votes, 'user_votes' => $user_votes);
}

function execute_answer_vote_ajax(){
  global $user;
  $poll_answer_id = get_request_param('poll_answer_id');
  $value = get_request_param('value');
  require_once('poll_votes.class.php');
  $poll_votes = new PollVotes(array('poll_answer_id' => $poll_answer_id, 'user_id' => $user['user_id']));
  logger(print_r($poll_votes,1));
  if(!count($poll_votes)){
    PollVotes::create($poll_answer_id, $user['user_id'], $value);
  }else{
    $poll_votes->first()->update(array('value' => $value));
  }
  $poll_votes = new PollVotes(array('poll_answer_id' => $poll_answer_id, 'value' => '1'));
  echo json_encode(array('count' => count($poll_votes)));
  exit;
}

function execute_answer_add_ajax(){
  global $user;
  $poll_id = get_request_param('poll_id');
  $answer = get_request_param('value');
  $answer = trim($answer);
  if(!empty($answer)){
    require_once('poll_answers.class.php');
    $poll_answers = new PollAnswers(array('poll_id' => $poll_id, 'answer' => $answer));
    logger(print_r($poll_answers,1));
    if(!count($poll_answers)){
      $poll_answer_id = PollAnswers::create($poll_id, $user['user_id'], $answer);
    }else{
      $poll_answer_id = $poll_answers->first()->poll_answer_id;
    }

    $value = 1;
    require_once('poll_votes.class.php');
    $poll_votes = new PollVotes(array('poll_answer_id' => $poll_answer_id, 'user_id' => $user['user_id']));
    logger(print_r($poll_votes,1));
    if(!count($poll_votes)){
      PollVotes::create($poll_answer_id, $user['user_id'], $value);
    }else{
      $poll_votes->first()->update(array('value' => $value));
    }
  }else{
    $poll_answer_id = 0;
  }
  echo json_encode(array('location_href' => '/polls/poll?poll_id='.$poll_id.'&poll_answer_id='.$poll_answer_id.'#poll_answer'.$poll_answer_id));
  exit;
}