<?php
$PROPERTIES['pathbar']=array(
  '/deliveries' => 'Anlieferungen',
  '/delivery?delivery_id='.$delivery->id => format_date($delivery->created,'j.n.Y').' '.$supplier->name
);
?>

<div class="row bottom_no_radius" id="delivery_head">
  <div class="col6">
    <div>
      <?php echo format_date($delivery->created,'j.n.Y H:i') ?><br>
      <span class="smaller"><?php #echo $delivery->creator->name ?></small>
    </div>
  </div>
  <div class="col4">
    <div>
      <div><b><?php echo $supplier->name ?></b></div>
    </div>
  </div>
  <div class="col4"></div>
  <div class="col1 right last">
    <div class="buttons">
      <div class="button trash mt0_5" onclick="active_input_post_value();delivery_delete('<?php echo $delivery->id ?>')">
        <i class="fa-regular fa-trash-can"></i>
      </div>
    </div>
  </div>
</div>


<?php foreach($delivery_items as $delivery_item): ?>
  <?php
    $product_id = $delivery_item->product_id;
    $product = $products[$product_id];
    $brand = '';
    if($supplier->producer == 2 && $product->brand_id){
      $brand = $brands[$product->brand_id];
    }
  ?>
  <div class="row product" data-id="<?php echo $product_id ?>" data-delivery_id="<?php echo $delivery->id ?>" data-item_id="<?php echo $delivery_item->id ?>">
    <div class="col2">
      <div style="display:block;width:3.5em;height:3.5em;background-color:rgba(255,255,255,0.5);border:1px solid black;border-radius:0.5em;"></div>
    </div>
    <div class="col5">
      <div>
        <?php echo ($supplier->id == 35)?htmlentities($product->supplier_product_id.' '):'' ?>
        <?php echo htmlentities($product->name) ?><br>
        <i style="font-size:80%"><?php echo htmlentities(trim($brand.' '.$supplier->name)) ?></i>
      </div>
    </div>
    <div class="col8">
      <?php if($product->type!='k'): ?>
        <div class="button large <?php echo $locked_less?'disabled':'' ?>" <?php echo $locked_less?'':'onclick="delivery_change(this,\'-\')"' ?>>-</div>
      <?php else: ?>
        <div style="width:1.7em;font-size:2em;">&nbsp;</div>
      <?php endif ?>
      <div class="" style="width:6em;text-align:right;margin-right:0.2em;">
        <?php /*if($amount_ordered): ?>
          <span><?php echo format_amount($amount_ordered) ?></span>
        <?php endif*/ ?>
        <?php if($product->type == 'p' || $product->type == 'w'): ?>
          <div class="input">
            <?php echo format_amount($delivery_item->amount_pieces); ?>
          </div>
          <span><?php echo translate_product_type_amount($product->type); ?></span><br>
        <?php endif ?>
        <?php if($product->type == 'k' || $product->type == 'w'): ?>
          <div class="input">
            <?php echo format_amount($delivery_item->amount_weight); ?>
          </div>
          <span><?php echo translate_product_type_amount('k'); ?></span><br>
        <?php endif ?>
        <div style="font-size:70%;cursor:help;">
          <?php if($product->type == 'w'): ?>
            <span>ca.(!) <?php echo format_weight($product->kg_per_piece) ?> kg / St.</span><br>
          <?php endif ?>
        </div>
      </div>
      <?php if($product->type!='k'): ?>
        <div class="button large <?php echo $locked_more?'disabled':'' ?>" <?php echo $locked_more?'':'onclick="delivery_change(this,\'+\')"' ?>>+</div>
      <?php else: ?>
        <div style="width:1.7em;font-size:2em;">&nbsp;</div>
      <?php endif ?>
      <?php if($product->type!='p'): ?>
        <div class="button large <?php echo $locked?'disabled':'' ?> <?php echo $amount_weight?'':'needs_todo' ?>" <?php echo $locked?'':'onclick="scale_show(this)"' ?> style="margin-left:0.2em" data-title="<?php echo htmlentities($product->name) ?>" data-value_exact="<?php echo $amount_ordered_weight ?>" data-value_min="<?php echo $amount_ordered_weight*0.8 ?>" data-value_max="<?php echo $amount_ordered_weight*1.2 ?>">
          <i class="fa-solid fa-weight-scale"></i>
        </div>
      <?php else: ?>
        <?php /*
        <div class="button large <?php echo $locked?'disabled':'' ?> <?php echo $amount!=$amount_ordered?'needs_todo':'' ?>" <?php echo $locked?'':'onclick="delivery_change(this,\'=\')"' ?> style="margin-left:0.2em">
          <i class="fa-solid fa-check"></i>
        </div>
        */?>
      <?php endif ?>
    </div>
  </div>
<?php endforeach ?>

<?php require('scale.part.php'); ?>
<?php require('keyboard.part.php'); ?>


<?php if($supplier->id == 35): ?>
  <div class="row">
    <div class="inner_row">
      <div class="col8">
        <b>Weiteres geliefertes Produkt anlegen</b>
      </div>
    </div>
    <div class="inner_row mt1">
      <div class="col2">
        Artikelnr
      </div>
      <div class="col2">
        <?php echo html_input(array(
          'type' => 'input_text',
          'field' => 'supplier_product_id',
          'value' => $supplier_product_id,
          )); ?>
      </div>
      <div class="col2">
        <div class="button" onclick="delivery_add_product()">anlegen</div>
      </div>
    </div>
    <?php if(isset($error)): ?>
      <div class="inner_row mt1">
        <b><?php echo htmlentities($error) ?></b>
      </div>
    <?php endif ?>
  </div>
<?php endif ?>

<div style="height:20em;"></div>