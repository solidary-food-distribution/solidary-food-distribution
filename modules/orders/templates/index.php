<?php
$PROPERTIES['pathbar']=array('/orders'=>'Bestellungen');
$first = 1;
?>

<?php foreach($orders as $order): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col12">
        <div>
          <b>Bestellung zum <?php echo format_date($order->pickup_date) ?></b><br>
          <?php foreach($purchase_dates[$order->pickup_date] as $purchase): ?>
            <?php echo $members[$purchase->supplier_id]->purchase_name ?><br>
          <?php endforeach ?>
          </div>
        </div>
      <div class="col2 right last">
        <?php if(isset($order->active) || $first): ?>
          <?php $first = 0; ?>
          <span class="button large" onclick="location.href='/order/?order_id=<?php echo $order->id ?>';">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </span>
        <?php else: ?>
          <span class="button large disabled">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </span>
        <?php endif ?>
      </div>
    </div>
  </div>
<?php endforeach ?>
