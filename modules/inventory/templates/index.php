<?php
$PROPERTIES['pathbar']=array(
  '/inventory' => 'Inventur',
);
?>

<?php 
  foreach($inventories as $i_id=>$item){
    require('item.part.php');
  }
?>

<div class="main_button button" onclick="location.href='/inventory/products';">Produkt hinzufügen</div>