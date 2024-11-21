<?php
$PROPERTIES['pathbar']=array('/orders'=>'Bestellungen');
?>

<?php foreach($orders as $order): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col4">
        <a href="/order/?order_id=<?php echo $order->id ?>"><?php echo format_date($order->pickup_date) ?></a>
      </div>
    </div>
  </div>
<?php endforeach ?>
