<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  '/admin/purchases'=>'Lieferanten Bestellungen',
  '' => format_date($purchase->datetime, 'j.n.Y H:i').' '.$supplier->name
);
$PROPERTIES['body_class']='header_h5 footer_h8';
?>

<div class="row">
  <div class="inner_row">
    <div class="col8">
      <b><?php echo htmlentities(format_date($purchase->datetime, 'j.n.Y H:i', false).' '.$supplier->name) ?></b>
    </div>
    <div class="col2 right">Benötigt</div>
    <div class="col2 right">Lager</div>
    <div class="col3 right">Bestellen</div>
    <div class="col3 right">EK netto</div>
  </div>
  <?php $sums = array(); ?>
  <?php foreach($product_sums as $product_id => $oi_sum): ?>
    <div class="inner_row">
      <div class="col8">
        <?php 
          echo htmlentities($products[$product_id]->name);
        ?>
      </div>
      <div class="col2 right">
        <?php
          if($oi_sum['amount_weight_needed']){
            echo format_weight($oi_sum['amount_weight_needed']).' kg';
          }else{
            echo $oi_sum['amount_pieces_needed'].' St.';
          }
        ?>
      </div>
      <div class="col2 right">
        <?php
          if($oi_sum['amount_weight_inventory']){
            echo format_weight($oi_sum['amount_weight_inventory']).' kg';
          }elseif($oi_sum['amount_pieces_inventory']){
            echo $oi_sum['amount_pieces_inventory'].' St.';
          }
        ?>
      </div>
      <div class="col3 right" style="display:inline;vertical-align:bottom;">
        <?php
          if($oi_sum['amount_weight']){
            echo format_weight($oi_sum['amount_weight']).' kg';
          }else{
            if(isset($oi_sum['amount_bundles'])){
              echo $oi_sum['amount_bundles'].' Gb. <small>(';
            }
            echo $oi_sum['amount_pieces'].' St.';
            if(isset($oi_sum['amount_bundles'])){
              echo ')</small>';
            }
          }
        ?>
      </div>
      <div class="col3 right">
        <?php
          $sums['netto'] += round($oi_sum['purchase_sum'] ,2);
          echo format_money($oi_sum['purchase_sum']).' EUR';
        ?>
      </div>
    </div>
  <?php endforeach ?>
  <div class="inner_row">
    <div class="col8"></div>
    <div class="col2 right"></div>
    <div class="col2 right"></div>
    <div class="col3 right"></div>
    <div class="col3 right"><b><?php echo format_money($sums['netto']) ?> EUR</b></div>
  </div>
  <div class="inner_row">
    <div class="col3 last right"><!--<a href="purchase_csv?supplier_id=<?php echo $supplier_id ?>">CSV-Datei</a>--></div>
  </div>
</div>
