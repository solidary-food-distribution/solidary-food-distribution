<?php
$PROPERTIES['pathbar']=array(
  '/inventory' => 'Inventur',
  '' => 'Produkt hinzufügen' 
);
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
  <div class="controls">
    <div class="control filter search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input class="filter" type="text" />
    </div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<div class="selection">
  <?php $type=''; $type_v=false; ?>
  <?php foreach($products as $product): ?>
    <?php if($product->type!=$type && $type!=''): ?>
      <div class="break"></div>
    <?php endif ?>
    <?php 
      $type=$product->type;
    ?>
    <div class="item" onclick="location.href='/inventory/product_select?product_id=<?php echo $product->id ?>'">
      <div class="image">
        <!--<img src="" />-->
      </div>
      <div class="info">
        <div class="label">
          <?php echo $product->name ?>
        </div>
        <div class="producer">
          <?php echo $product->producer->name ?>
        </div>
      </div>
    </div>
  <?php endforeach ?>
</div>

<div class="button main_button" onclick="location.href='/products/new?inventory=1'">Neues Produkt aufnehmen</div>
<div class="button main_button" onclick="location.href='/inventory'">Abbrechen</div>

