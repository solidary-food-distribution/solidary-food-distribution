<?php
$PROPERTIES['pathbar']=array('/activities'=>'Aktivitäten',''=>'Kalender');
$PROPERTIES['body_class']='header_h5';


?>

<?php ob_start(); ?>
<div class="controls">
  <div class="control_l input <?php echo $month_prev?'':'disabled' ?>" onclick="<?php echo $month_prev?'location.href=\'/tasks/calendar?month='.$month_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m input">
    <select class="center" name="month" onchange="location.href='/tasks/calendar?month='+$(this).val();">
      <?php foreach($months as $m=>$mv): ?>
        <option value="<?php echo $m ?>" <?php echo $month==$m?'selected="selected"':'' ?> ><?php echo $mv ?></option>
      <?php endforeach ?>
    </select>
  </div><div class="control_r input <?php echo $month_next?'':'disabled' ?>" onclick="<?php echo $month_next?'location.href=\'/tasks/calendar?month='.$month_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php
  $today=date('Y-m-d');
  $date=$start;
?>

<div class="weekdays labels">
  <div>Mo</div>
  <div>Di</div>
  <div>Mi</div>
  <div>Do</div>
  <div>Fr</div>
  <div>Sa</div>
  <div>So</div>
</div>

<?php do{ ?>
  <div class="week">
    <div class="weekdays">
    <?php for($weekday=1;$weekday<=7;$weekday++): ?>
      <div class="<?php echo (date('m',strtotime($date))==date('m',strtotime($month))?'acmo':'otmo').($date==$today?' today':'').(isset($tasks[$date])?' pointer':'') ?>" <?php echo isset($tasks[$date])?'onclick="location.href=\'/tasks/day?day='.$date.'\';"':'' ?> >
        <div class="day"><?php echo date('d',strtotime($date)) ?></div>
        <?php if(isset($tasks[$date])): ?>
          <?php foreach($tasks[$date] as $task): ?>
            <div class="task"><?php echo $task->title ?></div>
          <?php endforeach ?>
        <?php endif ?>
      </div>
      <?php $date=date('Y-m-d',strtotime("+1 days", strtotime($date))); ?>
    <?php endfor ?>
    </div>
  </div>
<?php } while(substr($date,0,7)<=substr($month,0,7)) ?>