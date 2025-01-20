<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Mitglieder Bestellungen'
);
$PROPERTIES['body_class']='header_h5 footer_h8';
?>

<?php foreach($product_sums as $member_id => $supplier_ids): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col8">
        <b><?php echo htmlentities($members[$member_id]->name) ?></b>
      </div>
    </div>
    <?php ksort($supplier_ids) ?>
    <?php foreach($supplier_ids as $supplier_id => $count): ?>
      <div class="inner_row">
        <div class="col3">
          <?php echo htmlentities($suppliers[$supplier_id]->name) ?>
        </div>
        <div class="col3 right">
          <?php echo $count ?> Artikel
        </div>
      </div>
    <?php endforeach ?>
  </div>
<?php endforeach ?>
