<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Mitglieder Bestellungen'
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
  <div class="control_l input <?php echo $date_prev?'':'disabled' ?>" onclick="<?php echo $date_prev?'location.href=\'/admin/orders?date='.$date_prev.'\'':'' ?>">
    <i class="fa-solid fa-caret-left"></i>
  </div><div class="control_m input">
    Abholung: <?php echo format_date($date).($date_next?' - '.format_date($date_until):''); ?>
  </div><div class="control_r input <?php echo $date_next?'':'disabled' ?>" onclick="<?php echo $date_next?'location.href=\'/admin/orders?date='.$date_next.'\'':'' ?>">
    <i class="fa-solid fa-caret-right"></i>
  </div>
  <?php if(count($members) && $date_until >= date('Y-m-d') && $date_prev < date('Y-m-d')): ?>
    <div class="button" onclick="location.href='/admin/pickup_emails?date=<?php echo $date ?>';">Noch Abholende E-Mails</div>
  <?php endif ?>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($members as $member_id => $member): ?>
  <div class="row">
    <?php foreach($member_orders[$member_id] as $order_id => $order): ?>
      <div class="inner_row">
        <div class="col9">
          <b><?php echo htmlentities($members[$member_id]->name) ?></b>
        </div>
        <div class="col2 right">
          Bestellt
        </div>
        <div class="col3 right">
          Abgeholt
        </div>
      </div>
      <?php ksort($order_items_array[$order->id]) ?>
      <?php foreach($order_items_array[$order->id] as $order_item): ?>
        <?php 
          $product = $products[$order_item->product_id];
          $pickup_item = $pickup_items_array[$member_id][$order_item->id];
          unset($pickup_items_array[$member_id][$order_item->id]);
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
      <?php foreach($pickup_items_array[$member_id] as $pickup_item): ?>
        <?php 
          $product = $products[$pickup_item->product_id];
        ?>
        <div class="inner_row">
          <div class="col9">
            <?php if($product->supplier_id == 35): ?>
              <?php echo htmlentities($product->supplier_product_id) ?>
            <?php endif ?>
            <?php echo htmlentities($product->name) ?>
          </div>
          <div class="col2 right">
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
