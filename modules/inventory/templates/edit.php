<?php
$PROPERTIES['pathbar']=array(
  '/inventory' => 'Inventur',
  '' => $inventory->product->name
);
?>

<div class="row product" id="inventory<?php echo $inventory->id ?>">
  <div class="col2">
    <div class="image">
      <!--<img src="" />-->
    </div>
  </div>
  <div class="col4">
    <div class="info">
      <div class="name">
        <b><?php echo $inventory->product->name ?></b>
      </div>
      <div class="producer">
        <?php echo $inventory->product->producer->name ?>
      </div>
    </div>
  </div>
  <div class="col4">
    <div class="amount_ctrl">
      <div class="left">
        <?php
          if($inventory->product->type != 'p'){
            echo html_input(array(
              'field' => 'amount_weight', 
              'type' => 'weight',
              'info' => 'Vorhandene Menge '.$inventory->product->name.' kg',
              'url' => '/inventory/update_ajax?product_id='.$inventory->product->id,
              'value' => format_weight($inventory->amount_weight)
            )).' kg<br>';
          }
          if($inventory->product->type != 'k'){
            echo html_input(array(
              'field' => 'amount_pieces', 
              'type' => 'pieces',
              'info' => 'Vorhandene Menge '.$inventory->product->name.' Stück',
              'url' => '/inventory/update_ajax?product_id='.$inventory->product->id,
              'value' => format_amount($inventory->amount_pieces)
            )).' Stück';
          }
        ?>
      </div>
    </div>
  </div>
  <div class="col4 right last">
    <span class="button large" onclick="inventory_remove_product('<?php echo $inventory->product->id ?>')">
      <i class="fa-regular fa-trash-can"></i>
    </span>
    <span class="button large" onclick="active_input_post_value_goto('/inventory')">
      <i class="fa-solid fa-check"></i>
    </span>
  </div>
</div>

<?php require('keyboard.part.php'); ?>
<script type="text/javascript">
  //see main.js
  keyboard_ok_func = function(){
    active_input_post_value();
  }
  $('.input[data-field]').first().click();
</script>