<?php
$PROPERTIES['pathbar']=array('/orders'=>'Bestellungen',''=>format_date($order->pickup_date));
$PROPERTIES['body_class']='header_h5 footer_h8';
?>


<?php ob_start(); ?>
  <div class="controls">
    <div class="control filter">
      <?php
        $product_type = 'o';
        $options = array('o' => 'Warenkorb', 'd' => 'Direkt vom Erzeuger', 't' => 'Großhandel');
        echo html_input(array(
          'class' => 'filter',
          'onclick' => 'filter_options',
          'type' => 'options',
          'field' => 'product_type',
          'value' => $product_type,
          'options' => $options,
      )); ?>
    </div>
    <div class="control filter search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input class="filter" type="text" />
    </div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($order_items as $order_item_id => $order_item): ?>
  <?php $product = $products[$order_item->product_id]; ?>
  <div class="row">
    <div class="col12">
      <?php echo htmlentities($product->name) ?>
    </div>
  </div>
<?php endforeach ?>


<?php
  #$PROPERTIES['footer']=ob_get_clean();
?>