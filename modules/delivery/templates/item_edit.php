<?php
$PROPERTIES['pathbar']=array(
  '/deliveries' => 'Lieferungen',
  '/delivery?delivery_id='.$delivery->id.'&item_id='.$item->id => format_date($delivery->created,'j.n.Y').' '.$delivery->supplier->name,
  '/delivery/item_edit?delivery_id='.$delivery->id.'&item_id='.$item->id => $item->product->name,
);
?>

<div class="row product" id="delivery_item<?php echo $item->id ?>">
  <div class="col2">
    <div class="image">
      <!--<img src="" />-->
    </div>
  </div>
  <div class="col4">
    <div class="info">
      <div class="name">
        <div class="input left" onclick="location.href='/delivery/products?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>';">
          <?php echo $item->product->name ?>
        </div>
      </div>
      <?php if($item->product->producer->id != $delivery->supplier->id): ?>
        <div class="producer">
          <?php echo $item->product->producer->name ?>
        </div>
      <?php endif ?>
    </div>
  </div>
  <div class="col4">
    <div class="amount_ctrl">
      <div class="left">
        <?php
          if($item->product->type != 'p'){
            echo html_input(array(
              'field' => 'amount_weight', 
              'type' => 'weight',
              'info' => 'Gelieferte Menge '.$item->product->name.' kg',
              'url' => '/delivery/update_ajax?delivery_id='.$delivery->id.'&item_id='.$item->id,
              'value' => format_weight($item->amount_weight)
            )).' kg<br>';
          }
          if($item->product->type != 'k'){
            echo html_input(array(
              'field' => 'amount_pieces', 
              'type' => 'pieces',
              'info' => 'Gelieferte Menge '.$item->product->name.' Stück',
              'url' => '/delivery/update_ajax?delivery_id='.$delivery->id.'&item_id='.$item->id,
              'value' => format_amount($item->amount_pieces)
            )).' Stück';
          }
        ?>
      </div>
      <?php /*
      <div class="left mt0_5">
        <span class="input amount smaller">
          <?php echo format_amount($item->weight_min) ?>
        </span>
        <span class="smaller">
         bis 
        </span>
        <span class="input amount smaller"><?php echo format_amount($item->weight_max) ?></span> <span class="smaller">kg</span>
          <br>
          &Oslash; <span class="input amount smaller"><?php echo format_amount($item->weight_avg) ?></span> kg
      </div>
      */ ?>
    </div>
  </div>
  <div class="col4">
    <div class="amount">
      <?php 
        if($item->product->type == 'v'){
          echo html_input(array(
            'field' => 'purchase',
            'type' => 'money',
            'info' => 'Grundpreis '.$item->product->name.' gemäß Lieferschein',
            'url' => '/delivery/update_ajax?delivery_id='.$delivery->id.'&item_id='.$item->id,
            'value' => format_money($item->purchase)
          )). ' EUR / ';
          echo html_input(array(
            'field' => 'price_type',
            'type' => 'options',
            'info' => 'Grundpreis Einheit '.$item->product->name.' gemäß Lieferschein',
            'url' => '/delivery/update_ajax?delivery_id='.$delivery->id.'&item_id='.$item->id,
            'value' => $item->price_type,
            'options' => array('k' => 'kg', 'p' => 'Stück'),
          ));
        }else{
          //print_r($item);
        }
      ?>
    </div> 
  </div>
  <div class="col3 right">
    <div>
      <?php
        /*
        if($item->product->type == 'v'){
          echo html_input(array(
            'field' => 'purchase_sum',
            'type' => 'money',
            'info' => 'Zeilensumme '.$item->product->name.' gemäß Lieferschein',
            'url' => '/delivery/update_ajax?delivery_id='.$delivery->id.'&item_id='.$item->id,
            'value' => format_money($item->purchase_sum)
          )). ' EUR';
        }
        */
      ?>
    </div>
  </div>
  <div class="col1 right last">
    <div class="buttons">
      <div class="button ok" onclick="active_input_post_value_goto('/delivery?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>')">
        <i class="fa-solid fa-check"></i>
      </div>
      <br>
      <div class="button trash mt0_5" onclick="active_input_post_value();delivery_item_delete('<?php echo $delivery->id ?>', '<?php echo $item->id ?>')">
        <i class="fa-regular fa-trash-can"></i>
      </div>
    </div>
  </div>
</div>

<?php require('keyboard.part.php'); ?>
<script type="text/javascript">
  if($('.input[data-field="price_type"] .option.selected[data-value="p"]').length){
    $('.input[data-field="amount_pieces"]').first().click();
  }else{
    $('.input[data-field]').first().click();
  }
</script>