<?php
  $product = $products[$item->product_id];
?>
<div class="row product" id="delivery_item<?php echo $item->id ?>">
  <div class="col2">
    <div class="image">
      <!--<img src="" />-->
    </div>
  </div>
  <div class="col4">
    <div class="info">
      <div class="name">
        <b><?php echo htmlentities($product->name) ?></b>
      </div>
      <?php if($product->producer_id != $delivery->supplier_id): ?>
        <div class="producer">
          <?php echo $suppliers[$product->producer_id]->name ?>
        </div>
      <?php endif ?>
    </div>
  </div>
  <div class="col4">
    <div class="amount_ctrl">
      <?php if($item->amount_bundles): ?>
        <div class="amount">
          <?php echo format_amount($item->amount_bundles) ?> Gebinde
        </div>
      <?php endif ?>
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
      <?php if($item->purchase): ?>
        <?php echo format_money($item->purchase) ?> EUR
      <?php endif ?>
      <?php if($item->price_type): ?>
        /<br>
        <?php echo translate_product_type($item->price_type) ?>
      <?php endif ?>
    </div>
  </div>
  <div class="col3 right">
    <div class="price_sum">
      <?php if($item->purchase_sum): ?>
        <?php echo format_money($item->purchase_sum) ?> EUR
      <?php endif ?>
    </div>
  </div>
  <div class="col1 right last">
    <span class="button" onclick="location.href='/delivery/item_edit?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>';">
      <i class="fa-solid fa-pencil"></i>
    </span>
  </div>
</div>