<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Mitglieder Bestellungen'
);
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
<div class="controls">
  <div class="control_l input <?php echo $date_prev?'':'disabled' ?>" onclick="<?php echo $date_prev?'location.href=\'/admin/orders?date='.$date_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m input">
    <?php echo format_date($date).($date_next?' - '.format_date(date('Y-m-d',strtotime('-1 DAYS',strtotime($date_next)))):''); ?>
  </div><div class="control_r input <?php echo $date_next?'':'disabled' ?>" onclick="<?php echo $date_next?'location.href=\'/admin/orders?date='.$date_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($members as $member_id => $member): ?>
  <div class="row">
    <?php foreach($member_orders[$member_id] as $order_id => $order): ?>
      <div class="inner_row">
        <div class="col8">
          <b><?php echo htmlentities($members[$member_id]->name) ?></b>
        </div>
      </div>
      <?php ksort($order_items_array[$order->id]) ?>
      <?php foreach($order_items_array[$order->id] as $order_item): ?>
        <?php 
          $product = $products[$order_item->product_id];
          $pickup_item = $pickup_items_array[$order_item->id];
          unset($pickup_items_array[$order_item->id]);
        ?>
        <div class="inner_row">
          <div class="col9">
            <?php if($product->supplier_id == 35): ?>
              <?php echo htmlentities($product->supplier_product_id) ?>
            <?php endif ?>
            <?php echo htmlentities($product->name) ?>
          </div>
          <div class="col2 right">
            <?php if($product->type == 'k'): ?>
              <?php echo format_amount($order_item->amount_weight).' kg' ?>
            <?php else: ?>
              <?php echo format_amount($order_item->amount_pieces).' St.' ?>
            <?php endif ?>
          </div>
          <div class="col3 right">
            <?php if($product->type == 'k'): ?>
              <?php echo format_amount($pickup_item->amount_weight).' kg' ?>
            <?php else: ?>
              <?php echo format_amount($pickup_item->amount_pieces).' St.' ?>
            <?php endif ?>
          </div>
        </div>
      <?php endforeach ?>
    <?php endforeach ?>
  </div>
<?php endforeach ?>
