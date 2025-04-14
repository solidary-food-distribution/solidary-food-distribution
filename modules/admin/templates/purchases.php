<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Lieferanten Bestellungen'
);
$PROPERTIES['body_class']='header_h5 footer_h8';
?>

<?php
$date_until = '';
if($date_next){
  $date_until = date('Y-m-d',strtotime('-1 DAYS',strtotime($date_next)));
}
?>

<?php ob_start(); ?>
<div class="controls">
  <div class="control_l input <?php echo $date_prev?'':'disabled' ?>" onclick="<?php echo $date_prev?'location.href=\'/admin/purchases?date='.$date_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m input">
    Abholung: <?php echo format_date($date).($date_next?' - '.format_date($date_until):''); ?>
  </div><div class="control_r input <?php echo $date_next?'':'disabled' ?>" onclick="<?php echo $date_next?'location.href=\'/admin/purchases?date='.$date_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($purchases as $purchase): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col5">
        <b><?php echo format_date($purchase->datetime, 'j.n.Y H:i') ?></b>
      </div>
      <div class="col10">
        <?php echo htmlentities($suppliers[$purchase->supplier_id]->name) ?>
      </div>
    </div>
    <div class="inner_row">
      <div class="col5">
        <small>für <?php echo format_date($delivery_dates[$purchase->delivery_date_id]->date, 'j.n.Y') ?></small>
      </div>
      <div class="col1 right last">
        <span class="button" onclick="location.href='/admin/purchase?purchase_id=<?php echo $purchase->id ?>';">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </span>
      </div>
    </div>
  </div>
<?php endforeach ?>
