<div class="row product" id="delivery_item<?php echo $item->id ?>">
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
      <div class="producer">
        <?php echo $item->product->producer->name ?>
      </div>
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
  <div class="col1 right last">
    <span class="button large" onclick="location.href='/inventory/edit?product_id=<?php echo $item->product->id ?>';">
      <i class="fa-solid fa-pencil"></i>
    </span>
  </div>
</div>