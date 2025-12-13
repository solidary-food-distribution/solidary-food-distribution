<?php
$PROPERTIES['pathbar']=array(
  '/pickups'=>'Abholungen',
  '/pickup?pickup_id='.$pickup->id => format_date($pickup->created,'j.n.Y H:i')
);
$PROPERTIES['body_class']='header_h5 footer_h8';
?>

<?php echo $start ?>

<?php if($start == 0): ?>
  <?php ob_start(); ?>
    <div class="controls" data-pickup-id="<?php echo $pickup->id ?>">
      <div class="control filter">
        <?php
          $options = array(
            'p' => '<i class="fa-solid fa-basket-shopping" title="Abholung"></i>'.($pickup_items_count?'<span class="count cart">'.$pickup_items_count.'</span>':'').' Abholung',
            'd' => '<i class="fa-solid fa-square-plus" title="Auf Lager"></i> Auf Lager'
          );
          echo html_input(array(
            'class' => 'filter',
            'onclick' => 'pickup_filter',
            'type' => 'options',
            'field' => 'modus',
            'value' => $modus,
            'options' => $options,
        )); ?>
      </div>
    </div>
  <?php $PROPERTIES['header']=ob_get_clean(); ?>
<?php endif ?>

<?php
  $sum = array();
  $open_pickup_items = array();
?>

<?php foreach($products as $product_id => $product): ?>
  <?php
    $amount_ordered = 0;
    $amount_ordered_type = '';
    $amount_inventory = 0;
    $amount_others = 0;
    $scale_title = '';
    $scale_minmax = 0.1;
    $order_item_comment = '';
    if(isset($pickup_items[$product_id])){
      $pickup_item = $pickup_items[$product_id];
      $order_item = $order_items[$pickup_item->order_item_id];
      if($order_item->split_status == 's'){
        $order_item->amount_pieces = 0;
        $order_item->amount_weight = 0;
      }elseif($order_item->split_status == 'o'){
        $split_data = json_decode($order_item->split_data, 1);
        $order_item->amount_pieces = $split_data['ordered'];
      }
      $order_item_comment = $order_item->comment;
      if($product->type == 'k'){
        $amount_ordered = $order_item->amount_weight;
        $amount_ordered_type = 'k';
        $amount_price = $pickup_item->amount_weight;
        $amount = $pickup_item->amount_weight;
        $amount_weight = $pickup_item->amount_weight;
        $amount_ordered_weight = $order_item->amount_weight;
        $scale_title = format_amount($amount_ordered_weight).' kg '.htmlentities($product->name);
      }elseif($product->type == 'w'){
        $amount_ordered = $order_item->amount_pieces;
        $amount_ordered_type = 'p';
        $amount = $pickup_item->amount_pieces;
        $amount_weight = $pickup_item->amount_weight;
        $amount_ordered_weight = $order_item->amount_pieces * $product->kg_per_piece;
        $amount_price = $pickup_item->amount_weight;
        $scale_title = format_amount($amount_ordered).' St. '.htmlentities($product->name).'<br><span style="font-size:60%">ca.(!) '.format_amount($product->kg_per_piece).' kg / St.</span>';
        $scale_minmax = 0.3;
      }else{
        $amount_ordered = $order_item->amount_pieces;
        $amount_ordered_type = 'p';
        $amount_price =  $pickup_item->amount_pieces;
        $amount = $pickup_item->amount_pieces;
      }
      $price = $pickup_item->price;
    }else{
      $amount_price = 0;
      $amount = 0;
    }
    if(isset($inventory[$product_id])){
      $amount_inventory = $inventory[$product_id]['amount_pieces'];
      if(isset($others['product_orders'][$product_id])){
        foreach($others['product_orders'][$product_id] as $others_amount){
          $amount_others += $others_amount;
        }
      }
    }
    if($amount_ordered && !$amount){
      $open_pickup_items[] = $product->name;
    }

    
    if($amount && $prices[$product_id]->price_bundle && $prices[$product_id]->amount_per_bundle){
      if($amount >= $prices[$product_id]->amount_per_bundle){
        $price = $prices[$product_id]->price_bundle;
      }
    }

    $price_row = round($price * $amount_price, 2);
    $purchase_incl_tax = round($amount_price * round($prices[$product_id]->purchase + $prices[$product_id]->purchase * ($prices[$product_id]->tax/100), 2), 2);
    #logger($prices[$product_id]->purchase." purchase_incl_tax $purchase_incl_tax");
    $supplier = $suppliers[$product->supplier_id];
    $brand = '';
    $locked = false;
    if($pickup->status != 'o'){
      $locked = true;
    }
    if(isset($order_item) && $order_item->split_status == 's'){
      $locked = true;
    }
    $locked_less = false;
    if($locked || $amount <= 0){
      $locked_less = true;
    }
    $locked_more = false;
    $locked_more_html = '';
    if($locked){
      $locked_more = true;
    }
    if(isset($amount_inventory) && $amount_inventory>0 && $amount_inventory-$amount_others <= 0){
      $locked_more = true;
      if($amount_others){
        $locked_more_html = 'title="'.$amount_others.' sind von weiteren Abholern reserviert." onclick="show_title(this)"';
      }
    }

    if($supplier->producer == 1){
      $sum['supplier_paid'] = $sum['supplier_paid'] + $purchase_incl_tax;
      $sum['supplier_sum'] = $sum['supplier_sum'] + $price_row;
    }elseif($supplier->producer == 2){
      $sum['trader_paid'] = $sum['trader_paid'] + $purchase_incl_tax;
      $sum['trader_sum'] = $sum['trader_sum'] + $price_row;
      if($product->brand_id){
        $brand = $brands[$product->brand_id];
      }
    }

    $price_title = "EK: ".format_money(round($prices[$product_id]->purchase + $prices[$product_id]->purchase * ($prices[$product_id]->tax/100), 2))." EUR";
    if($prices[$product_id]->suggested_retail){
      $price_title .= ", UVP: ".format_money($prices[$product_id]->suggested_retail)." EUR";
    }
    
    $sum['sum'] = $sum['sum'] + $price_row;
  ?>
  <div class="row product" data-id="<?php echo $product_id ?>" data-pickup_id="<?php echo $pickup->id ?>" data-item_id="<?php echo $pickup_item->id ?>">
    <?php if(!empty($order_item_comment)): ?>
      <div class="inner_row">
        <div class="col7"></div>
        <div class="col10">
          <span style="font-weight:bold; font-size: 80%; position:relative; top:-0.2em"><i><?php echo htmlentities($order_item_comment) ?></i></span>
        </div>
      </div>
    <?php endif ?>
    <div class="inner_row">
      <div class="col2">
        <div class="image" style="display:block;width:3.5em;height:3.5em;background-color:rgba(255,255,255,0.5);border:1px solid black;border-radius:0.5em;">
          <?php
            $infos = array();
            if(!empty($product->infos)){
              $infos = json_decode($product->infos, true);
              if(strpos($infos['link'], 'duckduckgo')){
                $infos = array();
              }
            }
            if(isset($infos['link'])){
              echo '<a href="'.$infos['link'].'" target="_blank">';
            }
            if(isset($infos['image'])){
              echo '<img src="'.$infos['image'].'" />';
            }
            if(isset($infos['link'])){
              echo '</a>';
            }
          ?>
        </div>
      </div>
      <div class="col5">
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
            <span class="amount_ordered"><?php echo format_amount($amount_ordered) ?></span>
          <?php endif ?>
          <?php if($modus == 'd' && $amount_inventory): ?>
            <span class="amount_ordered"><?php echo format_amount($amount_inventory) ?></span>
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
            if($modus == 'd'){
              $needs_todo = 0;
            }
            if(!isset($pickup_item->order_item_id) || $pickup_item->order_item_id == 0){
              $needs_todo = 0;
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
            <?php if($product->status == 'o' || $order_item->split_status != 'n'): ?>
              <span><?php echo format_money($prices[$product_id]->price) ?> EUR / <?php echo translate_product_type($product->type); ?></span>
            <?php endif ?>
            <?php if($prices[$product_id]->price_bundle && $prices[$product_id]->amount_per_bundle): ?>
              <br>
              <span><?php echo $prices[$product_id]->amount_per_bundle ?>: <?php echo format_money($prices[$product_id]->price_bundle) ?> EUR / <?php echo translate_product_type($product->type); ?></span>
            <?php endif ?>
          </div>
        </div>
        <?php if($product->type!='k'): ?>
          <div class="button large <?php echo $locked_more?'disabled':'' ?>" <?php echo $locked_more?$locked_more_html:'onclick="pickup_change(this,\'+\')"' ?>>+</div>
        <?php else: ?>
          <div style="width:1.7em;font-size:2em;">&nbsp;</div>
        <?php endif ?>
        <?php if($product->type != 'p'): ?>
          <?php 
            $scale_bottom = '';
            if($product->type == 'k' || $product->type == 'w'){
              if($others['product_amounts'][$product_id]){
                $others_count = count($others['product_orders'][$product_id]);
                $scale_bottom = 'Weitere Abholende: <b>'.$others_count.' mit gesamt '.format_amount($others['product_amounts'][$product_id]).' '.($product->type=='k'?'kg':'St.').'</b>';
              }else{
                $scale_bottom = 'Keine weiteren Abholende für dieses Produkt.';
              }
            }
          ?>
          <div class="button large <?php echo $locked?'disabled':'' ?> <?php echo $amount_weight?'':'needs_todo' ?>" <?php echo $locked?'':'onclick="scale_show(this)"' ?> style="margin-left:0.2em" data-title="<?php echo htmlentities($scale_title) ?>" data-value_exact="<?php echo $amount_ordered_weight ?>" data-value_min="<?php echo $amount_ordered_weight*(1-$scale_minmax) ?>" data-value_max="<?php echo $amount_ordered_weight*(1+$scale_minmax) ?>" data-bottom="<?php echo htmlentities($scale_bottom) ?>">
            <i class="fa-solid fa-weight-scale"></i>
          </div>
        <?php elseif($modus != 'd' && $amount_ordered > 0): ?>
          <div class="button large <?php echo $locked?'disabled':'' ?> <?php echo $amount!=$amount_ordered?'needs_todo':'' ?>" <?php echo $locked?'':'onclick="pickup_change(this,\'=\')"' ?> style="margin-left:0.2em">
            <i class="fa-solid fa-check"></i>
          </div>
        <?php endif ?>
      </div>
      <div class="col3 right last">
        <span><?php echo format_money($price * $amount_price) ?> EUR</span>
      </div>
    </div>
  </div>
<?php endforeach ?>

<?php require('scale.part.php'); ?>

<?php if($start == 0): ?>
  <?php ob_start(); ?>
    <?php if($modus == 'p'): ?>
      <div class="row">
        <div class="inner_row">
          <div class="col2"></div>
          <div class="col3 right"><small>Einkauf</small></div>
          <div class="col3 right"><small>-&gt; Geno</small></div>
          <div class="col5"></div>
          <div class="col2 right">Summe</div>
          <div class="col3 right last">
            <?php echo format_money($sum['sum']); ?> EUR
          </div>
        </div>
        <div class="inner_row">
          <div class="col2"><small>Erzeuger</small></div>
          <div class="col3 right"><small><?php echo format_money($sum['supplier_paid']) ?> EUR</small></div>
          <div class="col3 right"><small><?php echo format_money($sum['supplier_sum'] - $sum['supplier_paid']) ?> EUR</small></div>
          <div class="col8 right last">
            <?php if(!empty($open_pickup_items)): ?>
              <b>Noch <?php echo count($open_pickup_items) ?> Position<?php echo count($open_pickup_items)>1?'en':'' ?> offen</b>
            <?php endif ?>
          </div>
        </div>
        <div class="inner_row">
          <div class="col2"><small>Großhandel</small></div>
          <div class="col3 right"><small><?php echo format_money($sum['trader_paid']) ?> EUR</small></div>
          <div class="col3 right"><small><?php echo format_money($sum['trader_sum'] - $sum['trader_paid']) ?> EUR</small></div>
        </div>
        <div class="inner_row mt1">
          <div class="col12">
            <small>
            <?php if($others['others']): ?>
              Weitere Abholende: <?php echo $others['others'] ?>
            <?php else: ?>
              Nach Dir keine weiteren Abholenden.
            <?php endif ?>
            <?php #print_r($others) ?>
            </small>
          </div>
          <div class="col6 right last">
            <small>Die Abholung bleibt gespeichert</small>
          </div>
          <!--<div class="col8 right last">
            <div class="button disabled">Bestellung abschicken</div>
          </div>
        -->
        </div>
      </div>
    <?php endif ?>
  <?php
    $PROPERTIES['footer']=ob_get_clean();
  ?>
<?php endif ?>

<script>
  <?php
    $products = implode('[BR]', $open_pickup_items);
    $products = preg_replace("/[^0-9a-zA-Z ,.äöüÄÖÜß()[]-]/", "", $products);
  ?>
  $('.logout').attr('onclick', "pickup_check_logout('<?php echo htmlentities($products) ?>')");
</script>