<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Lieferanten Bestellungen'
);
$PROPERTIES['body_class']='header_h5';
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
      <div class="col6">
        <span class="onoff" onclick="admin_purchase_status(<?php echo $purchase->id ?>);"><i class="fa-solid fa-toggle-<?php echo $purchase->status=='a'?'on':'off' ?>"></i></span>&nbsp;
        <b class="<?php echo $purchase->status=='a'?'':'disabled' ?>"><?php echo format_date($purchase->datetime, 'j.n.Y H:i') ?></b>
        <span class="button small" onclick="admin_purchase_date(<?php echo $purchase->id ?>,'','');">
          <i class="fa-solid fa-pencil"></i>
        </span>
      </div>
      <div class="col10 <?php echo $purchase->status=='a'?'':'disabled' ?>">
        <?php echo htmlentities($suppliers[$purchase->supplier_id]->name) ?>
      </div>
    </div>
    <div class="inner_row">
      <div class="col5 <?php echo $purchase->status=='a'?'':'disabled' ?>">
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
