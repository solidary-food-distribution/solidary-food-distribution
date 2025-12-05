<?php
$PROPERTIES['pathbar']=array('/orders'=>'Bestellungen',''=>format_date($order->pickup_date));
$body_class='header_h5';
if($modus == 'o'){
  $body_class .= ' footer_h8';
}
$PROPERTIES['body_class']=$body_class;
?>

<?php echo $start ?>

<?php if($start == 0): ?>
  <?php ob_start(); ?>
    <div class="controls" data-order-id="<?php echo $order->id ?>">
      <div class="control filter">
        <?php
          $options = array(
            'o' => '<i class="fa-solid fa-basket-shopping" title="Warenkorb"></i>'.($order_items_count?'<span class="count cart">'.$order_items_count.'</span>':'').' <span class="label">Warenkorb</span>',
            'f' => '<i class="fa-solid fa-heart" title="Lieblinge"></i> <span class="label">Lieblinge</span>',
            '1' => '<i class="fa-solid fa-tractor" title="Direkt vom Erzeuger"></i> <span class="label">Erzeuger</span>',
            '2' => '<i class="fa-solid fa-warehouse" title="Vom Handel"></i> <span class="label">Handel</span>',
            /*'g' => '<i class="fa-solid fa-box-open"></i> <span class="label">Aufteilen</span>',*/
            's' => '<i class="fa-solid fa-magnifying-glass" title="Produktsuche"></i> <span class="label">Suche</span>'
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
    </div>
  <?php $PROPERTIES['header']=ob_get_clean(); ?>
<?php endif ?>

<?php if($modus == 's' && $start == 0): ?>
  <div class="input" style="margin:auto;width:50%;display:block;margin-top:0.5em;padding:0.5em;">
    <div style="display:block"><small>Produktname, Hersteller, Strichcode-Nummer...</small></div>
    <div style="display:block">
      <input class="filter" type="text" id="search" value="<?php echo htmlentities($search) ?>" onkeyup="order_search_keyup(event)" style="padding:0.2em;" />
      <div class="button search" id="search_button" onclick="order_filter(this)" style="padding-top:0em;padding-bottom:0em;";>suchen</div>
    </div>
  </div>

  <div>
    <?php ksort($categories); ?>
    <?php foreach($categories as $category => $ccount): ?>
      <div class="button category<?php echo isset($scategories[$category])?' selected':'' ?>" onclick="order_filter(this)" data-value="<?php echo htmlentities($category) ?>"><?php echo htmlentities($category).' ('.$ccount.')' ?></div>
    <?php endforeach ?>
  </div>

  <?php if(count($products) == 0 && $search!=''): ?>
    <div class="row">
      Keine Produkte gefunden
    </div>
  <?php endif ?>
<?php endif ?>


<?php
  $sum = array();
  $infos_lazy_load = array();
  $product_count = 0;
?>

<?php foreach($products as $product_id => $product): ?>
  <?php
    $product_count++;
    if($modus == 's' && $product_count >= $limit){
      break;
    }
    if(isset($order_items[$product_id])){
      $order_item = $order_items[$product_id];
      if($product->type == 'k'){
        $amount_price = $order_item->amount_weight;
        $amount =$order_item->amount_weight;
      }elseif($product->type == 'w'){
        $amount_price = $order_item->amount_pieces * $product->kg_per_piece;
        $amount = $order_item->amount_pieces;
      }else{
        $amount_price =  $order_item->amount_pieces;
        $amount = $order_item->amount_pieces;
      }
    }else{
      $amount_price = 0;
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

    $price_row = round($price * $amount_price, 2);
    $purchase_incl_tax = round($amount_price * round($prices[$product_id]->purchase + $prices[$product_id]->purchase * ($prices[$product_id]->tax/100), 2), 2);
    #logger($prices[$product_id]->purchase." purchase_incl_tax $purchase_incl_tax");
    $supplier = $suppliers[$product->supplier_id];
    $locked_less = true;
    $locked_more = true;
    #logger("product_id $product_id");
    if(isset($supplier_unlocked[$supplier->id])){
      #logger("supplier_unlocked");
      $locked_less = false;
      $locked_more = false;
    }elseif(isset($inventory[$product_id]['amount_pieces'])){
      #logger("inventory ".$inventory[$product_id]['amount_pieces']);
      if($inventory[$product_id]['amount_pieces'] > $amount){
        $locked_more = false;
      }
      if($amount > 0){
        $locked_less = false;
      }
    }
    if($locked_less && $amount > 0 && $supplier->producer == 2 && $product->status == 'o' ){
      $locked_less = false;
    }elseif($locked_less && $amount == 0 && $modus == 'o'){
      $locked_less = false;
    }
    if($supplier->producer == 1){
      $sum['supplier_paid'] = $sum['supplier_paid'] + $purchase_incl_tax;
      $sum['supplier_sum'] = $sum['supplier_sum'] + $price_row;
    }elseif($supplier->producer == 2){
      $sum['trader_paid'] = $sum['trader_paid'] + $purchase_incl_tax;
      $sum['trader_sum'] = $sum['trader_sum'] + $price_row;
    }

    $brand = '';
    if($product->brand_id){
      $brand = $brands[$product->brand_id];
    }

    $price_title = "EK: ".format_money(round($prices[$product_id]->purchase + $prices[$product_id]->purchase * ($prices[$product_id]->tax/100), 2))." EUR";
    if($prices[$product_id]->suggested_retail){
      $price_title .= ", UVP: ".format_money($prices[$product_id]->suggested_retail)." EUR";
    }
    
    $sum['sum'] = $sum['sum'] + $price_row;
  ?>
  <div class="row product" data-id="<?php echo $product_id ?>">
    <div class="col2">
      <div class="image" style="display:block;width:3.5em;height:3.5em;background-color:rgba(255,255,255,0.5);border:1px solid black;border-radius:0.5em;">
        <?php
          $infos = array();
          if(!empty($product->infos)){
            $infos = json_decode($product->infos, true);
            if($infos['date'] < date('Y-m-d')){
              $infos_lazy_load[] = $product_id;
            }
          }elseif($product->supplier_id == 35){
            $infos_lazy_load[] = $product_id;
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
      <div class="favorite<?php echo isset($favorites[$product_id])?' set':'' ?>" onclick="favorite_set(this)"></div>
    </div>
    <div class="col6">
      <div>
        <?php echo htmlentities($product->name) ?><br>
        <i style="font-size:80%"><?php echo htmlentities(trim($brand.' '.$supplier->name)) ?></i>
      </div>
    </div>
    <div class="col7">
      <div class="button large <?php echo $locked_less?'disabled':'' ?>" <?php echo $locked_less?'':'onclick="order_change(this,-1)"' ?>>-</div>
      <div class="" style="width:7em;text-align:right;margin-right:0.2em;">
        <?php if(0 && $amount && $prices[$product_id]->price_bundle && $prices[$product_id]->amount_per_bundle && $product->status == 's'): ?>
          <div class="input">
            <i class="fa-solid fa-box-open" style="color:#999"></i>
          </div>
        <?php endif ?>
        <div class="input">
          <?php echo format_amount($amount); ?>
        </div>
        <span><?php echo translate_product_type_amount($product->type); ?></span>
        <div style="font-size:70%;cursor:help;" title="<?php echo htmlentities($price_title) ?>" onclick="show_title(this)">
          <?php if($product->type == 'w'): ?>
            <span>ca.(!) <?php echo format_weight($product->kg_per_piece) ?> kg / St.</span><br>
          <?php endif ?>
          <?php if($product->status == 'o' || $product->status == 'e' || (!$prices[$product_id]->price_bundle)): ?>
            <span><?php echo format_money($prices[$product_id]->price) ?> EUR / <?php echo translate_product_type($product->type); ?></span>
          <?php endif ?>
          <?php if($prices[$product_id]->price_bundle && $prices[$product_id]->amount_per_bundle): ?>
            <br>
            <span><?php echo $prices[$product_id]->amount_per_bundle ?>: <?php echo format_money($prices[$product_id]->price_bundle) ?> EUR / <?php echo translate_product_type($product->type); ?></span>
          <?php endif ?>
        </div>
      </div>
      <div class="button large <?php echo $locked_more?'disabled':'' ?>" <?php echo $locked_more?'':'onclick="order_change(this,1)"' ?>>+</div>
    </div>
    <div class="col3 right last">
      <span><?php echo format_money($price * $amount_price) ?> EUR</span>
    </div>
  </div>
<?php endforeach ?>

<?php if($modus=='s' && $product_count>0 && $product_count==$limit): ?>
  <div id="show_more" class="button" style="float:right;margin:0.5em" onclick="order_show_more_load()">Weitere anzeigen...</div>
  <script>
    $('main').on('scroll',order_show_more);
  </script>
<?php endif ?>

<?php if(!empty($infos_lazy_load)): ?>
  <script>
    order_infos_lazy_load('<?php echo implode(',', $infos_lazy_load); ?>');
  </script>
<?php endif ?>

<?php if(!empty($notify)): ?>
  <script>
    notify('<?php echo htmlentities($notify) ?>');
  </script>
<?php endif ?>

<?php if($start == 0): ?>
  <?php ob_start(); ?>
    <?php if($modus == 'o'): ?>
      <div class="row">
        <div class="inner_row">
          <div class="col2"></div>
          <div class="col3 right"><small>Einkauf</small></div>
          <div class="col3 right"><small>-&gt; Geno</small></div>
          <div class="col2"></div>
          <div class="col5 right">Summe<?php 
            if($_SESSION['member']['order_limit']){
              echo ' (max '.intval($_SESSION['member']['order_limit']).' EUR)';
            }
          ?></div>
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
            <?php if(floor($order_sum_oekoring) < 300 && $order_sum_oekoring >= 0): ?>
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
<?php endif ?>