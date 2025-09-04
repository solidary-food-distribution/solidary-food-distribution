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
          /*'1' => '<i class="fa-solid fa-tractor" title="Erzeuger"></i> Erzeuger',*/
          '2' => '<i class="fa-solid fa-warehouse" title="Großhandel"></i> Großhandel',
          's' => '<i class="fa-solid fa-magnifying-glass" title="Suche"></i> Suche'
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

<?php if($modus == 's' && $start == 0): ?>
  <div class="input" style="margin:auto;width:50%;display:block;margin-top:0.5em;padding:0.5em;">
    <div style="display:block"><small>Produktname, Hersteller, Strichcode-Nummer...</small></div>
    <div style="display:block">
      <input class="filter" type="text" id="search" value="<?php echo htmlentities($search) ?>" onkeyup="inventory_search_keyup(event)" style="padding:0.2em;" />
      <div class="button search" id="search_button" onclick="inventory_filter(this)" style="padding-top:0em;padding-bottom:0em;";>suchen</div>
    </div>
  </div>

  <?php if(count($products) == 0 && $search!=''): ?>
    <div class="row">
      Keine Produkte gefunden
    </div>
  <?php endif ?>
<?php endif ?>

<?php foreach($products as $product): ?>
  <?php
    $supplier = $suppliers[$product->supplier_id];
    $brand = $brands[$product->brand_id];
    $pdata = $data[$product->id];
    $amount = 0;
    $amount_weight = 0;
    if($pdata['user_id']){
      if($product->type == 'k'){
        $amount = $pdata['amount_weight'];
      }
      if($product->type == 'p' || $product->type == 'w'){
        $amount = $pdata['amount_pieces'];
      }
      if($product->type == 'w'){
        $amount_weight = $pdata['amount_weight'];
      }
    }
    $product_name = $product->name;
    if($product->supplier_id == 35){
      $product_name = $product->supplier_product_id.' '.$product_name;
    }
  ?>
  <div class="row product" data-id="<?php echo $product->id ?>">
    <div class="col2">
      <div style="display:block;width:3.5em;height:3.5em;background-color:rgba(255,255,255,0.5);border:1px solid black;border-radius:0.5em;"></div>
    </div>
    <div class="col7">
      <div>
        <?php echo htmlentities($product_name) ?><br>
        <i style="font-size:80%"><?php echo htmlentities(trim($brand.' '.$supplier->name)) ?></i>
      </div>
    </div>
    <div class="col9">
      <?php if($product->type == 'k' || $pdata['amount_pieces'] == 0): ?>
        <div class="button large" onclick="inventory_update(this,'0')">0</div>
      <?php elseif($product->type != 'k'): ?>
        <div class="button large " onclick="inventory_update(this,'-')">-</div>
      <?php else: ?>
        <div style="width:1.7em;font-size:2em;">&nbsp;</div>
      <?php endif ?>
      <div class="" style="width:7.5em;text-align:right;margin-right:0.2em;">
        <?php if($pdata['user_id']): ?>
        <?php elseif($product->type == 'p' || $product->type == 'w'): ?>
          <span><?php echo format_amount($pdata['amount_pieces']) ?></span>
        <?php elseif($product->type == 'k'): ?>
          <span><?php echo format_amount($pdata['amount_weight']) ?></span>
        <?php endif ?>
        <div class="input <?php echo $pdata['user_id']?'':'needs_todo' ?>">
          <?php echo format_amount($amount); ?>
        </div>
        <span><?php echo translate_product_type_amount($product->type); ?></span><br>
        <?php if($product->type == 'w'): ?>
          <span><?php echo format_amount($pdata['amount_weight']) ?></span>
          <div class="input <?php echo $pdata['user_id']?'':'needs_todo' ?>">
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
        <div class="button large" onclick="inventory_update(this,'+')">+</div>
      <?php else: ?>
        <div style="width:1.7em;font-size:2em;">&nbsp;</div>
      <?php endif ?>
      <?php if($product->type!='p'): ?>
        <div class="button large  <?php echo $pdata['user_id']?'':'needs_todo' ?> disabled" onclick="alert('Noch nicht umgesetzt');return;scale_show(this)" style="margin-left:0.2em" data-title="<?php echo htmlentities($product->name) ?>" data-value_exact="<?php echo $amount_weight ?>" data-value_min="<?php echo $amount_weight*0.8 ?>" data-value_max="<?php echo $amount_weight*1.2 ?>">
          <i class="fa-solid fa-weight-scale"></i>
        </div>
      <?php elseif($pdata['amount_pieces'] > 0 && !$pdata['user_id']): ?>
        <div class="button large" onclick="inventory_update(this,'=')" style="margin-left:0.2em">
          <i class="fa-solid fa-check"></i>
        </div>
      <?php endif ?>
    </div>
  </div>
<?php endforeach ?> 

<?php /*
<div class="main_button button" onclick="location.href='/inventory/products';">Produkt hinzufügen</div>
*/ ?>