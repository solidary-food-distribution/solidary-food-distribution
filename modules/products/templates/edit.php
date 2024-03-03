<?php
$PROPERTIES['pathbar']=array(
  '/admin' => 'Administration',
  '/products' => 'Produkte',
  '' => $product->name,
);
?>

<div class="row product" id="product<?php echo $product->id ?>">
  <div class="col2">
    <div class="image">
      <!--<img src="" />-->
    </div>
  </div>
  <div class="col4">
    <div class="info">
      <div class="name">
        <?php
          echo html_input(array(
                'field' => 'name', 
                'type' => 'string',
                'info' => 'Produktname',
                'url' => '/products/update_ajax?product_id='.$product->id,
                'value' => $product->name
              ));
        ?>
      </div>
      <div class="producer">
        <?php echo $product->producer->name ?>
      </div>
    </div>
  </div>
  <div class="col3">
    <div class="amount_ctrl">
      <div class="left">
        <?php
          echo html_input(array(
            'field' => 'type',
            'type' => 'options',
            'info' => 'Typ',
            'url' => '/products/update_ajax?product_id='.$product->id,
            'value' => $product->type,
            'options' => array('k' => 'kg', 'p' => 'Stück', 'b' => 'Budget' /*, 'v' => 'Variabel'*/),
          ));
        ?>
      </div>
    </div>
  </div>
  <div class="col4">
    <div class="amount">
      EK <?php 
        echo html_input(array(
          'field' => 'price',
          'type' => 'money',
          'info' => 'EK-Preis ohne Steuer',
          'url' => '/products/update_ajax?product_id='.$product->id,
          'value' => $product->price,
        )).' EUR';
      ?>
      <br>
      <?php
        echo html_input(array(
          'field' => 'tax',
          'type' => 'options',
          'info' => 'Steuerprozent',
          'url' => '/products/update_ajax?product_id='.$product->id,
          'value' => $product->tax,
          'options' => array('7' => '7%', '19' => '19%'),
        ));
      ?>
    </div> 
  </div>
  <div class="col4">
    <div>
      VK <?php 
        echo html_input(array(
          'field' => 'price_sale',
          'type' => 'money',
          'info' => 'VK-Preis inkl. Steuer',
          'url' => '/products/update_ajax?product_id='.$product->id,
          'value' => $product->price_sale,
        )).' EUR';
      ?>
    </div>
  </div>
  <div class="col1 right last">
    <div class="buttons">
      <div class="button ok" onclick="active_input_post_value_goto('/products/?product_id=<?php echo $product->id ?>')">
        <i class="fa-solid fa-check"></i>
      </div>
      <?php /*
      <br>
      <div class="button trash mt0_5" onclick="active_input_post_value();products_delete('<?php echo $product->id ?>')">
        <i class="fa-regular fa-trash-can"></i>
      </div>
      */ ?>
    </div>
  </div>
</div>

<?php require('keyboard.part.php'); ?>
<script type="text/javascript">
  $('.input[data-field]').first().click();
</script>