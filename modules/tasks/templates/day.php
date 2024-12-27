<?php
$PROPERTIES['pathbar']=array(
  '/activities' => 'Aktivitäten',
  '/tasks/calendar?month='.substr($day,0,7) => 'Kalender',
  '' => format_date($day)
);
$PROPERTIES['body_class']='header_h5';

global $user;
?>

<?php ob_start(); ?>
<div class="controls">
  <div class="control_l input <?php echo $day_prev?'':'disabled' ?>" onclick="<?php echo $day_prev?'location.href=\'/tasks/day?day='.$day_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m input">
    <?php echo format_date($day) ?> 
  </div><div class="control_r input <?php echo $day_next?'':'disabled' ?>" onclick="<?php echo $day_next?'location.href=\'/tasks/day?day='.$day_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($tasks as $task): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col2">
        <?php echo substr($task->starts, 11, 5); ?>
      </div>
      <div class="col3">
        ca. <?php echo $task->effort ?> min
      </div>
      <div class="col8">
        <?php echo htmlentities($task->title) ?>
      </div>
    </div>
    <?php foreach($task_users[$task->task_id] as $task_user): ?>
      <?php
        if($task_user->user_id == $user['user_id']){
          continue;
        }
      ?>
      <div class="inner_row">
        <div class="col5"></div>
        <div class="col2 center">
          <?php echo $task_user->assign?'ja:':'' ?>
        </div>
        <div class="col10">
          <?php 
            echo htmlentities($users[$task_user->user_id]->name);
            if(!empty($task_user->comment)){
              echo ':<br>'.htmlentities($task_user->comment);
            }
          ?>
        </div>
      </div>
    <?php endforeach ?>
    <div class="inner_row mt0_5">
      <div class="col5"></div>
      <div class="col2">
        <?php echo html_input(array(
          'type' => 'options',
          'url' => '/tasks/assign_update_ajax?task_id='.$task->task_id,
          'field' => 'assign',
          'value' => $task_users[$task->task_id][$user['user_id']]->assign,
          'options' => array('100' => 'ja'),
        ));
        ?>
      </div>
      <div class="col10" style="padding-top:0.5em">
        <?php echo html_input(array(
          'type' => 'input_text',
          'url' => '/tasks/assign_update_ajax?task_id='.$task->task_id,
          'field' => 'comment',
          'value' => $task_users[$task->task_id][$user['user_id']]->comment,
        ));
        ?>
      </div>
    </div>
  </div>
<?php endforeach ?>
