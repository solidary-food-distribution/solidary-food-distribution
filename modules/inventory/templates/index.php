<?php
$PROPERTIES['pathbar']=array(
  '/inventory' => 'Inventur',
);
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
  <div class="filters">
    <?php
      $p_types = array();
      foreach($inventories as $i_id => $item){
        $p_types[($item->product->type=='v')?'v':'o']=1;
      }
      $options = array('o' => 'bestellte Produkte', 'v' => 'Gemüseanteil');
      $options = array_intersect_key($options, $p_types);
      if($product_type != 'v'){
        $product_type = 'o';
      }
      if(count($options) > 1){
        echo html_input(array(
          'class' => 'filter',
          'onclick' => 'filter_options',
          'type' => 'options',
          'field' => 'product_type',
          'value' => $product_type,
          'options' => $options,
        ));
      }
    ?>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php 
  foreach($inventories as $i_id=>$item){
    if($item->product->type == 'v' && $product_type != 'v'){
      continue;
    }elseif($item->product->type != 'v' && $product_type != 'o'){
      continue;
    }
    require('item.part.php');
  }
?>

<div class="main_button button" onclick="location.href='/inventory/products';">Produkt hinzufügen</div>