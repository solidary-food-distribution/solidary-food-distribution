<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Lieferanten Bestellungen'
);
$PROPERTIES['body_class']='header_h5 footer_h8';
?>

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
