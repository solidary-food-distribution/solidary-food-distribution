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
  <div class="col4">
    <div>
      <div>
        <?php echo translate_product_type($product->type) ?>
      </div>
    </div>
  </div>
  <div class="col3">
    <div class="amount">
      <span title="EK ohne Steuer"><?php echo format_money($product->price) ?> EUR</span><br>
      <span title="Steuer"><?php echo $product->tax ?>%</span><br>
      <span title="EK inkl. Steuer"><?php $price_incl_tax = $product->price * (100 + $product->tax)/100;
        echo format_money($price_incl_tax) 
      ?> EUR</span>
    </div>
  </div>
  <div class="col3 right">
    <div class="amount">
      <span title="VK inkl. Steuer"><?php echo format_money($product->price_sale) ?> EUR</span><br>
      <br>
      <span title="Marge"><?php echo round(round($product->price_sale / $price_incl_tax, 2) * 100, 2)-100 ?>%</span>
    </div>
  </div>
  <div class="col1 right last">
    <span class="button" onclick="location.href='/products/edit?product_id=<?php echo $product->id ?>';">
      <i class="fa-solid fa-pencil"></i>
    </span>
  </div>
</div>