<?php
$PROPERTIES['pathbar']=array('/admin' => 'Administration', '/admin/polls'=>'Umfragen','' => $poll->title);
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
  <?php foreach($missing_members as $member): ?>
    <div class="inner_row">
      <div class="col6">
        <?php echo htmlentities($member->name) ?>
      </div>
      <div class="col10">
        <select style="width:100%" name="poll_answer" data-member_id="<?php echo $member->id ?>" onchange="admin_poll_update(this)">
          <option></option>
          <?php foreach($poll_answers as $poll_answer): ?>
            <option value="<?php echo $poll_answer->poll_answer_id ?>"><?php echo htmlentities($poll_answer->answer) ?></option>
          <?php endforeach ?>
        </select>
      </div>
    </div>
  <?php endforeach ?>
</div>

<div style="height:20em;"></div>