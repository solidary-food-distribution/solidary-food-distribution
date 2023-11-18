<?php
$PROPERTIES['pathbar']=array('/deliveries'=>'Lieferungen');
$PROPERTIES['body_class']='header_h5';
?>


<?php ob_start(); ?>
  <div class="controls">
    <div class="control">
      <span class="label" onclick="location.href='/delivery/new'">Neue Lieferung</span>
    </div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($deliveries as $delivery): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col3">
        <div><?php echo format_date($delivery['d_created'],'j.n.Y H:i') ?></div>
      </div>
      <div class="col2">
        <div><?php echo $delivery['producer_name'] ?></div>
      </div>
      <div class="col2">
        <div><?php echo $delivery['di_items'] ?> Positionen</div>
      </div>
      <div class="col2 right">
        <div><?php echo number_format($delivery['d_price_total'],2,',','') ?> EUR</div>
      </div>
    </div>
    <div class="inner_row">
      <div class="button">Bearbeiten</div>
    </div>
  </div>
<?php endforeach ?>