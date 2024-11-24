<?php
$PROPERTIES['pathbar']=array('/orders'=>'Bestellungen',''=>format_date($order->pickup_date));
$PROPERTIES['body_class']='header_h5 footer_h8';
?>


<?php ob_start(); ?>
  <div class="controls" data-order-id="<?php echo $order->id ?>">
    <div class="control filter">
      <?php
        $options = array(
          'o' => '<i class="fa-solid fa-cart-shopping" title="Warenkorb"></i>'.($order_items_count?'<span class="count cart">'.$order_items_count.'</span>':'').' Warenkorb',
          /*'p' => '<i class="fa-solid fa-heart" title="Beliebte Produkte"></i>',*/
          '1' => '<i class="fa-solid fa-tractor" title="Direkt vom Erzeuger"></i> Erzeuger', 
          '2' => '<i class="fa-solid fa-warehouse" title="Vom Großhandel"></i> Großhandel',
          's' => '<i class="fa-solid fa-magnifying-glass" title="Produktsuche"></i> Suche'
        );
        echo html_input(array(
          'class' => 'filter',
          'onclick' => 'order_filter',
          'type' => 'options',
          'field' => 'modus',
          'value' => $modus,
          'options' => $options,
      )); ?>
    </div>
    <!--
    <div class="control filter search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input class="filter" type="text" />
    </div>
  -->
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php if($modus == 's'): ?>
  <div class="input" style="margin:auto;width:50%;display:block;margin-top:0.5em;padding:0.5em;">
    <div style="display:block"><small>Produktname, Hersteller, Strichcode-Nummer...</small></div>
    <div style="display:block">
      <input class="filter" type="text" id="search" value="<?php echo htmlentities($search) ?>" onkeyup="order_search_keyup(event)" style="padding:0.2em;" />
      <div class="button search" id="search_button" onclick="order_filter(this)" style="padding-top:0em;padding-bottom:0em;";>suchen</div>
    </div>
  </div>

  <?php if(count($products) == 0 && $search!=''): ?>
    <div class="row">
      Keine Produkte gefunden
    </div>
  <?php endif ?>
<?php endif ?>


<?php
  $sum = array();
?>

<?php foreach($products as $product_id => $product): ?>
  <?php
    if(isset($order_items[$product_id])){
      $order_item = $order_items[$product_id];
      if($product->type == 'k'){
        $amount = $order_item->amount_weight;
      }else{
        $amount = $order_item->amount_pieces;
      }
    }else{
      $amount = 0;
    }

    $price = $prices[$product_id]->price;
    if($amount && $prices[$product_id]->price_bundle && $prices[$product_id]->amount_per_bundle){
      /*$abf = $amount / $prices[$product_id]->amount_per_bundle;
      if($abf == intval($abf)){
        $price = $prices[$product_id]->price_bundle;
      }*/
      if($amount >= $prices[$product_id]->amount_per_bundle){
        $price = $prices[$product_id]->price_bundle;
      }
    }

    $price_row = round($price * $amount, 2);
    $purchase_incl_tax = round($amount * round($prices[$product_id]->purchase + $prices[$product_id]->purchase * ($prices[$product_id]->tax/100), 2), 2);
    #logger($prices[$product_id]->purchase." purchase_incl_tax $purchase_incl_tax");
    $supplier = $suppliers[$product->supplier_id];
    $brand = '';
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
  <div class="row product" data-id="<?php echo $product_id ?>">
    <div class="col2">
      <div style="display:block;width:3.5em;height:3.5em;background-color:rgba(255,255,255,0.5);border:1px solid black;border-radius:0.5em;"></div>
    </div>
    <div class="col6">
      <div>
        <?php echo htmlentities($product->name) ?><br>
        <i style="font-size:80%"><?php echo htmlentities(trim($brand.' '.$supplier->name)) ?></i>
      </div>
    </div>
    <div class="col7">
      <div class="button large" onclick="order_change(this,-1)">-</div>
      <div class="" style="width:7em;text-align:right;margin-right:0.2em;">
        <div class="input">
          <?php echo format_amount($amount); ?>
        </div>
        <span><?php echo translate_product_type($product->type); ?></span>
        <div style="font-size:70%;cursor:help;" title="<?php echo htmlentities($price_title) ?>" onclick="show_title(this)">
          <?php if($product->status == 'o'): ?>
            <span><?php echo format_money($prices[$product_id]->price) ?> EUR / <?php echo translate_product_type($product->type); ?></span>
          <?php endif ?>
          <?php if($prices[$product_id]->price_bundle && $prices[$product_id]->amount_per_bundle): ?>
            <br>
            <span><?php echo $prices[$product_id]->amount_per_bundle ?>: <?php echo format_money($prices[$product_id]->price_bundle) ?> EUR / <?php echo translate_product_type($product->type); ?></span>
          <?php endif ?>
        </div>
      </div>
      <div class="button large" onclick="order_change(this,1)">+</div>
    </div>
    <div class="col3 right last">
      <span><?php echo format_money($price * $amount) ?> EUR</span>
    </div>
  </div>
<?php endforeach ?>

<?php ob_start(); ?>
  <div style="background:white; color:red;margin:0.5em;margin:0.2em;"><b>&gt;&gt;&gt; Die Preise werden erst am 1.12.24 fest stehen! &lt;&lt;&lt;</b></div>
  <?php if($modus == 'o'): ?>
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
      </div>
      <div class="inner_row">
        <div class="col2"><small>Großhandel</small></div>
        <div class="col3 right"><small><?php echo format_money($sum['trader_paid']) ?> EUR</small></div>
        <div class="col3 right"><small><?php echo format_money($sum['trader_sum'] - $sum['trader_paid']) ?> EUR</small></div>
      </div>
      <div class="inner_row">
        <div class="col12">
          <?php if(floor($order_sum_oekoring)<300): ?>
            <span style="font-size:70%;border:1px solid grey;border-radius:0.5em;padding:0.2em">Hinweis Ökoring-Bestellung: Noch <?php echo 300-floor($order_sum_oekoring) ?> EUR Bestellwert notwendig.</span>
          <?php endif ?>
        </div>
        <div class="col6 right last">
          <small>Der Warenkorb bleibt gespeichert</small>
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
 