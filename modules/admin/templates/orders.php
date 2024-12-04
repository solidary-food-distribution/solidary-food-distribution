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
      <div class="col2 right">Benötigt</div>
      <div class="col2 right">Bestellen</div>
      <div class="col3 right">EK netto</div>
      <div class="col3 right">EK brutto</div>
    </div>
    <?php $sums = array(); ?>
    <?php foreach($oi_sums as $product_id => $oi_sum): ?>
      <div class="inner_row">
        <div class="col8">
          <?php 
            echo htmlentities($oi_sum['name']);
          ?>
        </div>
        <div class="col2 right">
          <?php
            if($oi_sum['amount_needed_unit'] == 'kg'){
              echo format_weight($oi_sum['amount_needed']).' kg';
            }else{
              echo $oi_sum['amount_needed'].' '.$oi_sum['amount_needed_unit'];
            }
          ?>
        </div>
        <div class="col2 right">
          <?php
            if($oi_sum['amount_order_unit'] == 'kg'){
              echo format_weight($oi_sum['amount_order']).' kg';
            }else{
              echo $oi_sum['amount_order'].' '.$oi_sum['amount_order_unit'];
            }
          ?>
        </div>
        <div class="col3 right">
          <?php
            $sums['netto'] += round($oi_sum['sum_price'] ,2);
            echo format_money($oi_sum['sum_price']).' EUR';
          ?>
        </div>
        <div class="col3 right">
          <?php
            $price_brutto = $oi_sum['sum_price'] * (100 + $oi_sum['tax'])/100;
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
    <div class="inner_row">
      <div class="col3 last right"><a href="orders_csv?supplier_id=<?php echo $supplier_id ?>">CSV-Datei</a></div>
    </div>
  </div>
<?php endforeach ?>

<pre>
<?php

  #print_r($products);
?>
</pre>