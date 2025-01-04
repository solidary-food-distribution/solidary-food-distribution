<?php
$PROPERTIES['pathbar']=array(
  '/inventory' => 'Inventur',
);
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
  <div class="controls">
    <div class="control filter">
      <?php
        $options = array(
          '1' => '<i class="fa-solid fa-tractor" title="Erzeuger"></i> Erzeuger', 
          '2' => '<i class="fa-solid fa-warehouse" title="Großhandel"></i> Großhandel',
        );
        echo html_input(array(
          'class' => 'filter',
          'onclick' => 'inventory_filter',
          'type' => 'options',
          'field' => 'modus',
          'value' => $modus,
          'options' => $options,
      )); ?>
    </div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($products as $product): ?>
  <div class="row product">
    <div class="col2">
      <div style="display:block;width:3.5em;height:3.5em;background-color:rgba(255,255,255,0.5);border:1px solid black;border-radius:0.5em;"></div>
    </div>
    <div class="col8">
      <div>
        <?php echo htmlentities($product->name) ?><br>
        <i style="font-size:80%"><?php echo htmlentities(trim($brand.' '.$supplier->name)) ?></i>
      </div>
    </div>
    <div class="col8">
      <?php if($product->type!='k'): ?>
        <div class="button large <?php echo $locked_less?'disabled':'' ?>" <?php echo $locked_less?'':'onclick="pickup_change(this,\'-\')"' ?>>-</div>
      <?php else: ?>
        <div style="width:1.7em;font-size:2em;">&nbsp;</div>
      <?php endif ?>
      <div class="" style="width:6em;text-align:right;margin-right:0.2em;">
        <?php if($amount_ordered): ?>
          <span><?php echo format_amount($amount_ordered) ?></span>
        <?php endif ?>
        <?php
          $needs_todo = ($amount_ordered != $amount);
          if($product->type == 'k'){
            $needs_todo = 0;
            if($amount_weight > $amount_ordered_weight*1.2){
              $needs_todo = 1;
            }elseif($amount_weight < $amount_ordered_weight*0.8){
              $needs_todo = 1;
            }
          }
        ?>
        <div class="input <?php echo $needs_todo?'needs_todo':'' ?>">
          <?php echo format_amount($amount); ?>
        </div>
        <span><?php echo translate_product_type_amount($product->type); ?></span><br>
        <?php if($product->type == 'w'): ?>
          <div class="input <?php echo $amount_weight?'':'needs_todo' ?>">
            <?php echo format_amount($amount_weight); ?>
          </div>
          <span><?php echo translate_product_type_amount('k'); ?></span><br>
        <?php endif ?>
        <div style="font-size:70%;cursor:help;" title="<?php echo htmlentities($price_title) ?>" onclick="show_title(this)">
          <?php if($product->type == 'w'): ?>
            <span>ca.(!) <?php echo format_weight($product->kg_per_piece) ?> kg / St.</span><br>
          <?php endif ?>
        </div>
      </div>
      <?php if($product->type!='k'): ?>
        <div class="button large <?php echo $locked?'disabled':'' ?>" <?php echo $locked?'':'onclick="pickup_change(this,\'+\')"' ?>>+</div>
      <?php else: ?>
        <div style="width:1.7em;font-size:2em;">&nbsp;</div>
      <?php endif ?>
      <?php if($product->type!='p'): ?>
        <div class="button large <?php echo $locked?'disabled':'' ?> <?php echo $amount_weight?'':'needs_todo' ?>" <?php echo $locked?'':'onclick="scale_show(this)"' ?> style="margin-left:0.2em" data-title="<?php echo htmlentities($product->name) ?>" data-value_exact="<?php echo $amount_ordered_weight ?>" data-value_min="<?php echo $amount_ordered_weight*0.8 ?>" data-value_max="<?php echo $amount_ordered_weight*1.2 ?>">
          <i class="fa-solid fa-weight-scale"></i>
        </div>
      <?php else: ?>
        <div class="button large <?php echo $locked?'disabled':'' ?> <?php echo $amount!=$amount_ordered?'needs_todo':'' ?>" <?php echo $locked?'':'onclick="pickup_change(this,\'=\')"' ?> style="margin-left:0.2em">
          <i class="fa-solid fa-check"></i>
        </div>
      <?php endif ?>
    </div>
  </div>
<?php endforeach ?> 

<div class="main_button button" onclick="location.href='/inventory/products';">Produkt hinzufügen</div>