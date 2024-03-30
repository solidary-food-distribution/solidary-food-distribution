<div class="row product" id="product<?php echo $product->id ?>">
  <div class="col2">
    <div class="image">
      <!--<img src="" />-->
    </div>
  </div>
  <div class="col4">
    <div class="info">
      <div class="name">
        <b><?php echo $product->name ?></b>
      </div>
      <?php if($product->producer->id != $delivery->supplier->id): ?>
        <div class="producer">
          <?php echo $product->producer->name ?>
        </div>
      <?php endif ?>
    </div>
  </div>
  <div class="col2">
    <div>
      <div>
        <?php echo translate_product_type($product->type) ?><br>
        <span title="Steuer"><?php echo $product->tax ?>%</span><br>
      </div>
    </div>
  </div>
  <div class="col3 right">
    <div class="amount">
      <span title="EK ohne Steuer"><?php echo format_money($product->purchase) ?> EUR</span><br>
      <?php $purchase_tax = $product->purchase * $product->tax/100; ?>
      <span title="EK MwSt: <?php echo round($purchase_tax,5) ?>, EK+MwSt: <?php echo round($product->purchase + $purchase_tax,5) ?>"><?php 
        echo format_money($purchase_tax);
      ?> EUR</span><br>
    </div>
  </div>
  <div class="col3 right">
    <div class="amount">
      <span title="VK inkl. Steuer"><?php echo format_money($product->price) ?> EUR</span><br>
      <?php $price_tax = $product->price - $product->price / ((100 + $product->tax)/100); ?>
      <span title="VK Steuer: <?php echo round($price_tax,5) ?>"><?php echo format_money($price_tax);
      ?> EUR</span><br>
      <span title="Steuer Finanzamt: <?php echo round($price_tax-$purchase_tax,5) ?>"><?php echo format_money($price_tax-$purchase_tax);
      ?> EUR</span><br>
      
    </div>
  </div>
  <div class="col2 right">
    <div class="amount">
      <span title="Marge nach Steuerverrechnung"><?php
        $margin = 0;
        $in = $product->price - $price_tax + $purchase_tax;
        $out = $product->purchase + $purchase_tax;
        if($in){
          $margin = 100-round($out / $in, 2) * 100;
        }
        echo $margin;
      ?>%</span><br>
    </div>
  </div>
  <div class="col1 right last">
    <span class="button" onclick="location.href='/products/edit?product_id=<?php echo $product->id ?>';">
      <i class="fa-solid fa-pencil"></i>
    </span>
  </div>
</div>