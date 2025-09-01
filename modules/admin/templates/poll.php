<?php
$PROPERTIES['pathbar']=array('/admin' => 'Administration', '/admin/polls'=>'Umfragen',''=>$poll->title);
?>

<div class="row">
  <b><?php echo htmlentities($poll->title); ?></b><br>
  <?php echo nl2br($poll->text); ?>
</div>

<?php foreach($poll_answers as $poll_answer): ?>
  <div class="row">
    <div class="inner_row">
      <div>
        <b><?php echo htmlentities($poll_answer->answer); ?></b>
      </div>
    </div>
    <?php foreach($votes[$poll_answer->poll_answer_id] as $vote): ?>
      <div class="inner_row">
        <div class="col12">
          <?php echo htmlentities($users[$vote->user_id]->name) ?>
        </div>
      </div>
    <?php endforeach ?>
  </div>
<?php endforeach ?>

<div class="row">
  <div class="inner_row">
    <b>Noch nicht teilgenommen:</b>
  </div>
  <div class="inner_row">
    <?php foreach($missing_members as $member): ?>
      <?php echo htmlentities($member->name) ?><br>
    <?php endforeach ?>
  </div>
</div>

<div style="height:20em;"></div>