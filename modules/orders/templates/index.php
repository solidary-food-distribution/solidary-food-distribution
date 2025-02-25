<?php
$PROPERTIES['pathbar']=array('/orders'=>'Bestellungen');
$set = 0;
$from = date('Y-m-d', strtotime('-4 days', time()));
?>

<?php foreach($orders as $order): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col8">
        Bestellung zum <?php echo format_date($order->pickup_date) ?>
      </div>
      <div class="col1 right last">
        <?php if($order->pickup_date < $from || $set): ?>
          <span class="button large disabled">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </span>
        <?php else: ?>
          <?php 
            if($order->pickup_date > date('Y-m-d')){
              $set = 1;
            }
          ?>
          <span class="button large" onclick="location.href='/order/?order_id=<?php echo $order->id ?>';">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </span>
        <?php endif ?>
      </div>
    </div>
  </div>
<?php endforeach ?>
