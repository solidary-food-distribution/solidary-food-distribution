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
        <div class="input amount_weight" data-info="Gelieferte Menge <?php echo $item->product->name ?> kg" data-field="amount_weight" data-type="weight" data-unit="kg" data-url="/delivery/update_ajax?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>" data-value="<?php echo $item->amount_weight ?>" onclick="input_onfocus(this)">
          <?php echo format_weight($item->amount_weight) ?>
        </div> kg
        <br>
        <div class="input amount_pieces" data-info="Gelieferte Menge <?php echo $item->product->name ?> Stück" data-field="amount_pieces" data-type="pieces" data-url="/delivery/update_ajax?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>" data-value="<?php echo $item->amount_pieces ?>" onclick="input_onfocus(this)">
          <?php echo format_amount($item->amount_pieces) ?>
        </div> Stück
      </div>
      <!--
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
    -->
    </div>
  </div>
  <div class="col4">
    <div class="amount">
      <div class="input money" data-info="Grundpreis <?php echo $item->product->name ?> gemäß Lieferschein" data-field="price" data-type="money" data-url="/delivery/update_ajax?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>" data-value="<?php echo $item->price ?>" onclick="input_onfocus(this)"><?php echo format_money($item->price) ?></div> EUR / 
      <div class="input radio" data-info="Grundpreis Einheit <?php echo $item->product->name ?> gemäß Lieferschein" data-url="/delivery/update_ajax?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>" data-field="price_type" data-type="options" data-value="<?php echo $item->price_type ?>" onclick="input_onfocus(this)">
        <div class="option">
          <input type="radio" name="price_type<?php echo $item->id ?>" id="price_type_k<?php echo $item->id ?>" value="k" <?php echo $item->price_type=='k'?'checked="checked"':'' ?> /><label for="price_type_k<?php echo $item->id ?>"> kg</label>
        </div><div class="option">
          <input type="radio" name="price_type<?php echo $item->id ?>" id="price_type_p<?php echo $item->id ?>" value="p" <?php echo $item->price_type=='p'?'checked="checked"':'' ?> /><label for="price_type_p<?php echo $item->id ?>"> Stück</label>
        </div>
      </div>
    </div> 
  </div>
  <div class="col3 right">
    <div>
      <div class="input money" onclick="input_onfocus(this)" data-info="Zeilensumme <?php echo $item->product->name ?> gemäß Lieferschein" data-url="/delivery/update_ajax?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>" data-field="price_sum" data-type="money" data-value="<?php echo $item->price_sum ?>"><?php echo format_money($item->price_sum) ?></div> EUR
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
  $('.input[data-field]').first().click();
</script>