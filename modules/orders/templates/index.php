<?php
$PROPERTIES['pathbar']=array('/orders'=>'Bestellungen');
?>

<?php foreach($orders as $order): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col8">
        Bestellung zum <?php echo format_date($order->pickup_date) ?>
      </div>
      <div class="col1 right last">
        <span class="button large" onclick="location.href='/order/?order_id=<?php echo $order->id ?>';">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </span>
      </div>
    </div>
  </div>
<?php endforeach ?>
