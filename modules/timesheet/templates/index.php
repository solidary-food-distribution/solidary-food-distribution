<?php
$PROPERTIES['pathbar']=array('/activities'=>'Aktivitäten',''=>'Arbeitszeiten');
$PROPERTIES['body_class']='header_h5 footer_h4';
$sum=0;
?>

<?php
  ob_start();
?>
<div class="controls">
  <div class="control_l <?php echo $month_prev?'':'disabled' ?>" onclick="<?php echo $month_prev?'location.href=\'/timesheet?month='.$month_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m">
    <select class="center" name="month" onchange="location.href='/timesheet?month='+$(this).val();">
      <?php foreach($months as $m=>$mv): ?>
        <option value="<?php echo $m ?>" <?php echo $month==$m?'selected="selected"':'' ?> ><?php echo $mv ?></option>
      <?php endforeach ?>
    </select>
  </div><div class="control_r <?php echo $month_next?'':'disabled' ?>" onclick="<?php echo $month_next?'location.href=\'/timesheet?month='.$month_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
  <?php if($edit_id!='new'): ?>
    <div class="control" onclick="location.href='/timesheet/new?month=<?php echo $month ?>';">
      Neuer Eintrag
    </div>
  <?php endif ?>
</div>
<?php
  $PROPERTIES['header']=ob_get_clean();
?>

<?php foreach($timesheet as $id=>$entry): ?>
  <?php
    if(intval($entry['mins'])){
      $sum+=$entry['mins'];
    }
  ?>
  <div class="row entry">
    <?php if($edit_id==$id): ?>
      <div class="inner_row">
        <div class="col2 center">
          <div>
            <div id="input_date" data-value="<?php echo $entry['date'] ?>"><?php echo format_date($entry['date']) ?></div>
            <div class="button" onclick="timesheet_date_change(-1)"><i class="fa-solid fa-caret-left"></i></div>
            <div class="button" onclick="timesheet_date_change(1)"><i class="fa-solid fa-caret-right"></i></div>
          </div>
        </div>
        <div class="col2 right">
          <div><input id="input_mins" maxlength="3" type="text" value="<?php echo $entry['mins'] ?>" /> min</div>
        </div>
        <div class="col5">
          <div class="what"><input id="input_what" type="text" value="<?php echo htmlentities($entry['what']) ?>" /></div>
        </div>
      </div>
      <div class="inner_row mt1">
        <div class="col5 right last">
          <div class="button" onclick="location.href='/timesheet?month=<?php echo $month ?>';">Abbrechen</div>
          <div class="button" onclick="timesheet_save(this,'<?php echo $id ?>')">Speichern</div>
        </div>
      </div>
    <?php else: ?>
      <div class="col2 center">
        <div><?php echo format_date($entry['date']) ?></div>
      </div>
      <div class="col2 right">
        <div><?php echo $entry['mins'] ?> min</div>
      </div>
      <div class="col5">
        <div><?php echo htmlentities($entry['what']) ?></div>
      </div>
      <div class="col1 right last">
        <?php if(!$edit_id): ?>
          <span class="button" onclick="timesheet_edit(this,'<?php echo $id ?>')">
            <i class="fa-solid fa-pencil"></i>
          </span>
        <?php endif ?>
      </div>
    <?php endif ?>
  </div>
<?php endforeach ?>

<?php ob_start(); ?>
  <div class="row">
    <div class="col2 center">
      <div>
        <?php echo $months[$month] ?>
      </div>
    </div>
    <div class="col2 right">
      <div><?php echo $sum ?> min = <?php echo number_format($sum/60,1,',','') ?> Std</div>
    </div>
  </div>
<?php $PROPERTIES['footer']=ob_get_clean(); ?>