<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Infos'
);
$PROPERTIES['body_class']='header_h5';
?>

<?php /*ob_start(); ?>
<div class="controls">
  <div class="control_l input <?php echo $month_prev?'':'disabled' ?>" onclick="<?php echo $month_prev?'location.href=\'/timesheet?month='.$month_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m input">
    <select class="center" name="month" onchange="location.href='/timesheet?month='+$(this).val();">
      <?php foreach($months as $m=>$mv): ?>
        <option value="<?php echo $m ?>" <?php echo $month==$m?'selected="selected"':'' ?> ><?php echo $mv ?></option>
      <?php endforeach ?>
    </select>
  </div><div class="control_r input <?php echo $month_next?'':'disabled' ?>" onclick="<?php echo $month_next?'location.href=\'/timesheet?month='.$month_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
</div>
<?php $PROPERTIES['header']=ob_get_clean();*/ ?>

<?php foreach($infos as $info): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col4">
        <?php if($info->published == '0000-00-00 00:00:00'): ?>
          Entwurf<br>
          <?php echo format_date($info->created) ?>
        <?php else: ?>
          <b><?php echo format_date($info->published) ?></b>
        <?php endif ?>
      </div>
      <div class="col13">
        <b><?php echo htmlentities($info->subject) ?></b>
      </div>
      <div class="col1 right last">
        <span class="button" onclick="location.href='/admin/info?info_id=<?php echo $info->id ?>';">
          <i class="fa-solid fa-pencil"></i>
        </span>
      </div>
    </div>
    <div class="inner_row">
      <div class="col4"></div>
      <div class="col13">
        <div>
          <?php echo format_content($info->content) ?>
        </div>
      </div>

    </div>
  </div>
<?php endforeach ?>
