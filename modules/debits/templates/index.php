<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration','/debits'=>'Abbuchungen');
$PROPERTIES['body_class']='header_h5 footer_h4';
$totalsum=0;
?>
<?php ob_start(); ?>
<div class="controls">
  <div class="control_l input <?php echo $month_prev?'':'disabled' ?>" onclick="<?php echo $month_prev?'location.href=\'/debits?month='.$month_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m input">
    <select class="center" name="month" onchange="location.href='/debits?month='+$(this).val();">
      <?php foreach($months as $m=>$mv): ?>
        <option value="<?php echo $m ?>" <?php echo $month==$m?'selected="selected"':'' ?> ><?php echo $mv ?></option>
      <?php endforeach ?>
    </select>
  </div><div class="control_r input <?php echo $month_next?'':'disabled' ?>" onclick="<?php echo $month_next?'location.href=\'/debits?month='.$month_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
  <div class="button" onclick="debits_export('<?php echo $month ?>')">Exportieren</div>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($members as $member_id => $member): ?>
  <?php if(!isset($debits[$member_id])){ continue; } ?>
  <div class="row">
  <?php
    $sum=0;
  ?>
    <div class="inner_row">
      <div class="col8">
        <div><b><?php echo htmlentities($member->name) ?></b></div>
      </div>
    </div>
  <?php $actual_member_id = $member_id; ?>
  <?php foreach($debits[$member_id] as $debit_id => $debit): ?>
    <?php if($actual_member_id != $debit->member_id): ?>
      <?php $actual_member_id = $debit->member_id; ?>
      <div class="inner_row">
        <div class="col8">
          <div><b><?php echo htmlentities($members[$debit->member_id]->name) ?></b></div>
        </div>
      </div>
    <?php endif ?>
    <div class="inner_row">
      <div class="col13">
        <div>Abholung am <?php echo date('d.m.Y', strtotime($pickups[$debit->pickup_id]->created)) ?></div>
      </div>
      <div class="col1 right">
        <?php echo format_amount($debit->tax) ?>%
      </div>
      <div class="col4 right last">
        <div>
          <?php
            echo format_money($debit->amount);
            $sum += round($debit->amount, 2);
          ?> EUR
        </div>
      </div>
    </div>
  <?php endforeach ?>
    <div class="inner_row mb1">
      <div class="col4 right last">
        <div>
          <div><b><?php echo number_format($sum,2,',','') ?> EUR</b></div>
        </div>
      </div>
    </div>
  </div>
  <?php $totalsum+=$sum; ?>
<?php endforeach ?>
<?php
  ob_start();
?>
<div class="row">
  <div class="inner_row">
    <div class="col4 right last">
      <div>
        <?php echo number_format($totalsum,2,',',''); ?> EUR
      </div>
    </div>
  </div>
</div>
<?php
  $PROPERTIES['footer']=ob_get_clean();
?>