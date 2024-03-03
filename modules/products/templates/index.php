<?php
$PROPERTIES['pathbar']=array(
  '/admin' => 'Administration',
  '/products' => 'Produkte'
);
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>

<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php 
  foreach($products as $product){
    require('product.part.php');
  }
?>