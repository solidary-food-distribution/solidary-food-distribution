<div class="row product" id="delivery_item<?php echo $item->id ?>">
  <div class="col1">
    <div class="image">
      <!--<img src="" />-->
    </div>
  </div>
  <div class="col2">
    <div class="info">
      <div class="name">
        <?php echo $item->product->name ?>
      </div>
      <?php if($item->product->producer->id != $delivery->supplier->id): ?>
        <div class="producer">
          <?php echo $item->product->producer->name ?>
        </div>
      <?php endif ?>
    </div>
  </div>
  <div class="col2">
    <div class="amount_ctrl">
      <?php if($item->amount_weight): ?>
        <div class="amount">
          <?php echo format_amount(round($item->amount_weight,2)) ?> kg
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
  <div class="col2 right">
    <div class="amount">
      <?php echo format_money($item->price) ?>&nbsp;EUR&nbsp;/&nbsp;<?php echo translate_product_type($item->price_type) ?>
    </div>
  </div>
  <div class="col2 right">
    <div class="price_sum">
      <?php echo format_money($item->price_sum) ?>&nbsp;EUR
    </div>
  </div>
  <div class="col1 right last">
    <span class="button" onclick="alert('NOCH NICHT IMPLEMENTIERT');return false;ajax_id_replace('delivery_item<?php echo $item->id ?>', '/delivery/item_ajax?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>&edit=1')">
      <i class="fa-solid fa-pencil"></i>
    </span>
  </div>
</div>