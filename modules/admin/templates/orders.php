<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Mitglieder Bestellungen'
);
$PROPERTIES['body_class']='header_h5 footer_h8';
?>

<?php foreach($member_orders as $member_id => $order): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col8">
        <b><?php echo htmlentities($members[$member_id]->name) ?></b>
      </div>
    </div>
    <?php ksort($order_items_array[$order->id]) ?>
    <?php foreach($order_items_array[$order->id] as $order_item): ?>
      <?php $product = $products[$order_item->product_id]; ?>
      <div class="inner_row">
        <div class="col8">
          <?php if($product->supplier_id == 35): ?>
            <?php echo htmlentities($product->supplier_product_id) ?>
          <?php endif ?>
          <?php echo htmlentities($product->name) ?>
        </div>
        <div class="col3 right">
          <?php if($product->type == 'k'): ?>
            <?php echo format_amount($order_item->amount_weight).' kg' ?>
          <?php else: ?>
            <?php echo format_amount($order_item->amount_pieces).' St.' ?>
          <?php endif ?>
        </div>
      </div>
    <?php endforeach ?>
  </div>
<?php endforeach ?>
