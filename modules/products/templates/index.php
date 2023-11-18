<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration','/products'=>'Produkte');
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
  <div class="controls">
    <div class="control">
      <span class="label" onclick="location.href='/product/new'">Neues Produkt</span>
    </div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

NOCH NICHT UMGESETZT
