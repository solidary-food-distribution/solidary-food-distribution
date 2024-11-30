<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Bestellungen'
);
$PROPERTIES['body_class']='header_h5 footer_h8';
?>

<?php foreach($product_sums as $supplier_id => $oi_sums): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col8">
        <b><?php echo htmlentities($suppliers[$supplier_id]->name) ?></b>
      </div>
      <div class="col2 right">Stück</div>
      <div class="col2 right">Gewicht</div>
      <div class="col3 right">EK netto</div>
      <div class="col3 right">EK brutto</div>
    </div>
    <?php $sums = array(); ?>
    <?php foreach($oi_sums as $product_id => $oi_sum): ?>
      <?php $product = $products[$product_id]; ?>
      <div class="inner_row">
        <div class="col8">
          <?php echo htmlentities($product->name) ?>
        </div>
        <div class="col2 right">
          <?php echo ($product->type != 'k')?$oi_sum['amount_pieces'].' St.':'' ?>
        </div>
        <div class="col2 right">
          <?php
            $amount_weight = '';
            if($product->type == 'w'){
              $amount_weight = round($oi_sum['amount_pieces'] * $product->kg_per_piece , 2);
            }elseif($product->type == 'k'){
              $amount_weight = $oi_sum['amount_weight'];
            }
            echo $amount_weight.($amount_weight != ''?' kg':'');
          ?>
        </div>
        <div class="col3 right">
          <?php
            $price = $prices[$product_id]->purchase;
            if($product->type == 'p'){
              $price *= $oi_sum['amount_pieces'];
            }else{
              $price *= $amount_weight;
            }
            $sums['netto'] += round($price ,2);
            echo format_money($price).' EUR';
          ?>
        </div>
        <div class="col3 right">
          <?php
            $price_brutto = $price * (100 + $prices[$product_id]->tax)/100;
            $sums['brutto'] += round($price_brutto, 2);
            echo format_money($price_brutto).' EUR';
          ?>
        </div>
      </div>
    <?php endforeach ?>
    <div class="inner_row">
      <div class="col8"></div>
      <div class="col2 right"></div>
      <div class="col2 right"></div>
      <div class="col3 right"><b><?php echo format_money($sums['netto']) ?> EUR</b></div>
      <div class="col3 right"><b><?php echo format_money($sums['brutto']) ?> EUR</b></div>
    </div>
  </div>
<?php endforeach ?>

<pre>
<?php

  #print_r($products);
?>
</pre>