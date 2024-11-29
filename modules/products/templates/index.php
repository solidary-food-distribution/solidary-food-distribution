<?php
$PROPERTIES['pathbar']=array(
  '/admin' => 'Administration',
  '/products' => 'Produkte'
);
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
<div class="button" onclick="products_new()">Neues Produkt anlegen</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php 
  foreach($products as $product){
    require('product.part.php');
  }
?>