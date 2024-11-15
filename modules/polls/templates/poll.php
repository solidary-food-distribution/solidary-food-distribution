<?php
$PROPERTIES['pathbar']=array('/polls'=>'Umfragen',''=>$poll->title);
$PROPERTIES['body_class']='header_h5';

if($poll->close_datetime!='0000-00-00 00:00:00' && strtotime($poll->close_datetime) < time()){
  $poll_closed = true;
  $onclick='disabled';
}else{
  $poll_closed = false;
  $onclick='onclick="polls_answer_vote(this)"';
}

?>

<?php ob_start(); ?>
  <div class="controls">

  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>


<div class="row">
  <b><?php echo htmlentities($poll->title); ?></b><br>
  <?php echo nl2br($poll->text); ?>
</div>

<?php foreach($poll_answers as $poll_answer): ?>
  <div class="row">
    <div class="col12">
      <a name="poll_answer<?php echo $poll_answer->poll_answer_id ?>"></a>
      <b><?php echo htmlentities($poll_answer->answer); ?></b>
    </div>
    <div class="col3 last right">
      <div class="input">
        <input type="checkbox" value="<?php echo $poll_answer->poll_answer_id ?>" data-id="<?php echo $poll_answer->poll_answer_id ?>" <?php echo $onclick ?> <?php echo isset($user_votes[$poll_answer->poll_answer_id])?'checked':'' ?> />
        <span onclick="$(this).prev('input').click()"><i class="fa-solid fa-thumbs-up"></i></span>
        <span onclick="$(this).prevAll('input').click()" id="count<?php echo $poll_answer->poll_answer_id ?>"><?php echo isset($votes[$poll_answer->poll_answer_id])?$votes[$poll_answer->poll_answer_id]:0 ?></span>
        &nbsp;
      </div>
    </div>
  </div>
<?php endforeach ?>

<?php if(!$poll_closed): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col12">Weiteren Produktvorschlag hinzufügen:</div>
    </div>
    <div class="inner_row">
      <?php echo html_input(array(
        'type' => 'input_text',
        'field' => 'answer',
        'class' => 'col12',
        'url' => '/polls/answer_add_ajax?poll_id='.$poll->poll_id,
        )); ?>
    </div>
  </div>
<?php endif ?>

<div style="height:20em;"></div>