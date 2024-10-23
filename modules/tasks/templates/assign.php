<?php
global $user;
$PROPERTIES['pathbar']=array('/activities'=>'Aktivitäten',''=>'Aufgabenverteilung');
$PROPERTIES['body_class']='header_h5';
?>


<?php foreach($tasks as $task): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col6">
        <b><?php echo $task->title; ?></b>
      </div>
      <div class="col3">
        <?php
          $i=$task->interval;
          if($i=='e'){
            $i='bei Ereignis';
          }elseif($i=='d'){
            $i='täglich';
          }elseif($i=='w'){
            $i='wöchentlich';
          }elseif($i=='m'){
            $i='monatlich';
          }
          echo $i;
        ?><br>
        <?php echo 'ca. '.intval($task->effort).' min'; ?>
      </div>
      <?php
        $assign=0;
        $comment='';
        if(!empty($task->users) && $task->users[0]->user_id==$user['user_id']){
          $assign=$task->users[0]->assign;
          $comment=$task->users[0]->comment;
        }
      ?>
      <div class="col9 last">
        <?php echo html_input(array(
          'field' => 'assign',
          'type' => 'options',
          'info' => 'Mitmachen',
          'url' => '/tasks/assign_update_ajax?task_id='.$task->task_id,
          'value' => $assign,
          'options' => array('0' => '0', '10' => '10%', '25' => '25%', '50' => 'halb', '100' => 'voll'),
          )); ?>
      </div>
    </div>
    <div class="inner_row">
      <div class="col9 last">
          <?php echo  html_input(array(
            'field' => 'comment',
            'type' => 'string',
            'info' => 'Kommentar',
            'url' => '/tasks/assign_update_ajax?task_id='.$task->task_id,
            'value' => $comment,
            )); ?>
      </div>
    </div>
    <?php #logger(print_r($task->users,1)); ?>
    <?php foreach($task->users as $task_user): ?>
      <?php
        if($task_user->user_id == $user['user_id'] || ($task_user->assign == 0 && trim($task_user->comment) == '')){
          continue;
        }
      ?>
      <div class="inner_row">
        <div class="col12 last">
          <?php echo htmlentities($users[$task_user->user_id]->name) ?>: <?php echo $task_user->assign ?>% <?php echo htmlentities($task_user->comment) ?>
        </div>
      </div>
    <?php endforeach ?>
  </div>
<?php endforeach ?>


<?php require('keyboard.part.php'); ?>
<script type="text/javascript">
  $('.input[data-field]').first().click();
</script>