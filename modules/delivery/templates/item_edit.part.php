<div class="row product" id="delivery_item<?php echo $item->id ?>">
  <div class="col1">
    <div class="image">
      <!--<img src="" />-->
    </div>
  </div>
  <div class="col2">
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
  <div class="col2">
    <div class="amount_ctrl">
      <div class="amount">
        <input class="right" type="text" size="4" value="<?php echo format_amount(round($item->amount_weight,2)) ?>" /> kg
      </div>
      <div class="amount">
        <input class="right" type="text" size="4" value="<?php echo format_amount($item->amount_pieces) ?>" /> Stück
      </div>
      <div class="amount smaller mt0_5">
        <input class="right" type="text" size="3" value="<?php echo format_amount($item->weight_min) ?>" /> bis 
        <input class="right" type="text" size="3" value="<?php echo format_amount($item->weight_max) ?>" /> kg
        <br>
        &Oslash; <input class="right" type="text" size="3" value="<?php echo format_amount($item->weight_avg) ?>" /> kg
      </div>
    </div>
  </div>
  <div class="col2 right">
    <div class="amount">
      <input class="right" type="text" size="4" value="<?php echo format_money($item->price) ?>" /> EUR / 
      <br>
      <div class="input"><input type="radio" id="item_price_type<?php echo $item->id ?>_k" name="item_price_type<?php echo $item->id ?>" value="k" <?php echo $item->price_type=='k'?'checked="checked"':'' ?> /><label for="item_price_type<?php echo $item->id ?>_k"> kg</label></div>
      <div class="input"><input type="radio" id="item_price_type<?php echo $item->id ?>_p" name="item_price_type<?php echo $item->id ?>" value="p" <?php echo $item->price_type=='p'?'checked="checked"':'' ?> /><label for="item_price_type<?php echo $item->id ?>_p""> Stück</label></div>
    </div>
  </div>
  <div class="col2 right">
    <div class="price_sum">
      <input class="right" type="text" size="4" value="<?php echo format_money($item->price_sum) ?>" /> EUR
    </div>
  </div>
  <div class="col1 right last">
    <span class="button trash" onclick="delivery_item_delete('<?php echo $delivery->id ?>', '<?php echo $item->id ?>')">
      <i class="fa-regular fa-trash-can"></i>
    </span>
    <span class="button" onclick="ajax_id_replace('delivery_item<?php echo $item->id ?>', '/delivery/item_ajax?id=<?php echo $delivery->id ?>&item_id=<?php echo $item->id ?>')">
      <i class="fa-solid fa-check"></i>
    </span>
  </div>
</div>