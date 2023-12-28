<?php
$PROPERTIES['pathbar']=array(
  '/pickups'=>'Abholungen',
  '/pickup?pickup_id='.$pickup->id => format_date($pickup->created,'j.n.Y H:i')
);
$PROPERTIES['body_class']='header_h5 footer_h8';
?>

<?php ob_start(); ?>
  <div class="filters">
    <?php
      $p_types = array();
      foreach($pickup->items as $item){
        $p_types[$item->product->type]=1;
      }
      $options = array('p' => 'bestellte Stück-Produkte', 'k' => 'bestellte Wiege-Produkte', 'v' => 'variable Produkte');
      $options = array_intersect_key($options, $p_types);
      if(isset($filter['product_type'])){
        $product_type = $filter['product_type'];
      }else{
        $product_type = key($options);
      }
      echo html_input(array(
        'onclick' => 'filter_options',
        'field' => 'product_type',
        'type' => 'options',
        'url' => '/pickup/filter_ajax?pickup_id='.$pickup->id,
        'value' => $product_type,
        'options' => $options,
      ));
    ?>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($pickup->items as $item): ?>
  <div class="row product" id="pickup_item<?php echo $item->id ?>" data-product_type="<?php echo $item->product->type ?>" style="display:none;">
    <div class="col2">
      <div class="image">
        <!--<img src="" />-->
      </div>
    </div>
    <div class="col4">
      <div class="info">
        <div class="name">
          <b><?php echo $item->product->name ?></b>
        </div>
        <?php if($item->product->producer->id != $delivery->supplier->id): ?>
          <div class="producer">
            <?php echo $item->product->producer->name ?>
          </div>
        <?php endif ?>
      </div>
    </div>
    <div class="col4">
      <div class="amount_ctrl">
        <?php if($item->amount_weight): ?>
          <div class="amount">
            <?php echo format_amount($item->amount_weight) ?> kg
          </div>
        <?php endif ?>
        <?php if($item->amount_pieces): ?>
          <div class="amount">
            <?php echo format_amount($item->amount_pieces) ?> Stück
          </div>
        <?php endif ?>
        <?php if($item->weight_min || $item->weight_max || $item->weight_avg): ?>
          <div class="amount smaller mt0_5">
            <?php if($item->weight_min || $item->weight_max): ?>
              <?php echo format_amount($item->weight_min) ?> bis <?php echo format_amount($item->weight_max) ?> kg
            <?php endif ?>
            <?php if($item->weight_avg): ?>
              <br>
              &Oslash; <?php echo format_amount($item->weight_avg) ?> kg
            <?php endif ?>
          </div>
        <?php endif ?>
      </div>
    </div>
    <div class="col4">
      <div class="amount">
        <?php if($item->price): ?>
          <?php echo format_money($item->price) ?> EUR
        <?php endif ?>
        <?php if($item->price_type): ?>
          /<br>
          <?php echo translate_product_type($item->price_type) ?>
        <?php endif ?>
      </div>
    </div>
    <div class="col3 right">
      <div class="price_sum">
        <?php if($item->price_sum): ?>
          <?php echo format_money($item->price_sum) ?> EUR
        <?php endif ?>
      </div>
    </div>
  </div>
<?php endforeach ?>
<script type="text/javascript">
  $('.filters .options .option.selected').click();
</script>