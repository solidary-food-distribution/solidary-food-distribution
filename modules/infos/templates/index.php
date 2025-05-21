<?php
$PROPERTIES['pathbar']=array(
  ''=>'Infos'
);
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
<div class="controls">
  <div class="control_l input <?php echo $date_prev?'':'disabled' ?>" onclick="<?php echo $date_prev?'location.href=\'/infos?date='.$date_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m input">
    <select class="center" name="month" onchange="location.href='/infos?date='+$(this).val();">
      <?php foreach($dates as $d=>$dv): ?>
        <option value="<?php echo $d ?>" <?php echo $date==$d?'selected="selected"':'' ?> ><?php echo $dv ?></option>
      <?php endforeach ?>
    </select>
  </div><div class="control_r input <?php echo $date_next?'':'disabled' ?>" onclick="<?php echo $date_next?'location.href=\'/infos?date='.$date_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($infos as $info): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col4">
        <b><?php echo format_date($info->published) ?></b>
      </div>
      <div class="col13">
        <b><?php echo htmlentities($info->subject) ?></b>
      </div>
    </div>
    <div class="inner_row">
      <div class="col4"></div>
      <div class="col13 mt0_5">
        <div>
          <?php echo format_content($info->content) ?>
        </div>
      </div>
    </div>
  </div>
<?php endforeach ?>
