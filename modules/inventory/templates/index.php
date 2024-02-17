<?php
$PROPERTIES['pathbar']=array(
  '/inventory' => 'Inventur',
);
?>

<?php 
  foreach($delivery->items as $di_id=>$item){
    require('item.part.php');
  }
?>

Folgt...


<!--
<div class="main_button button" onclick="location.href='/inventory/products';">Neue Position anlegen</div>
-->