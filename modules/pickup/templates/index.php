<?php
$PROPERTIES['pathbar']=array(
  '/pickups'=>'Abholungen',
  '/pickup?pickup_id='.$pickup->id => format_date($pickup->created,'j.n.Y H:i')
);
$PROPERTIES['body_class']='header_h5 footer_h8';

$order_sum = 0;
$variable_sum = 0;
foreach($orders as $order){
  if($order->product->type == 'b'){
    $variable_sum += $order->amount;
  }else{
    $order_sum += $order->product->price * $order->amount;
  }
}
$pickup_order_sum = 0;
$pickup_variable_sum = 0;
?>

<?php ob_start(); ?>
  <div class="filters">
    <?php
      $p_types = array();
      foreach($pickup->items as $item){
        $p_types[($item->product->type=='v')?'v':'o']=1;
        if($item->product->type == 'v'){
          $pickup_variable_sum += $item->price_sum;
        }else{
          $pickup_order_sum += $item->price_sum;
        }
      }
      $options = array('o' => 'bestellte Produkte', 'v' => 'Gemüseanteil');
      $options = array_intersect_key($options, $p_types);
      if($product_type != 'v'){
        $product_type = 'o';
      }
      if(count($options) > 1){
        echo html_input(array(
          'class' => 'filter',
          'onclick' => 'filter_options',
          'type' => 'options',
          'field' => 'product_type',
          'value' => $product_type,
          'options' => $options,
        ));
      }
    ?>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($pickup->items as $item): ?>
  <?php
    if($item->product->type == 'v' && $product_type != 'v'){
      continue;
    }elseif($item->product->type != 'v' && $product_type != 'o'){
      continue;
    }
  ?>
  <div class="row product" id="pickup_item<?php echo $item->id ?>" data-pickup_id="<?php echo $pickup->id ?>" data-item_id="<?php echo $item->id ?>" data-product_type="<?php echo ($item->product->type=='v')?'v':'o' ?>">
    <div class="col2">
      <div class="image">
        <!--<img src="" />-->
      </div>
    </div>
    <div class="col4">
      <div class="info">
        <div class="name">
          <b><?php echo $item->product->name ?></b>
        </div>
        <?php if($item->product->producer->id != $delivery->supplier->id): ?>
          <div class="producer">
            <?php echo $item->product->producer->name ?>
          </div>
        <?php endif ?>
      </div>
    </div>
    <div class="col4">
      <div class="amount">
        <?php if($item->amount_order): ?>
          <div>
            <?php echo format_amount($item->amount_order).' '.translate_product_type($item->product->type); ?>
          </div>
        <?php endif ?>
      </div>
    </div>
    <div class="col4">
      <div class="amount_ctrl">
        <?php //print_r($item); ?>
        <?php if($item->price_type == 'p'): ?>
          <div class="amount">
            <b><?php echo $item->amount_pieces ?> <?php echo translate_product_type($item->price_type) ?></b>
          </div>
          <div class="ctrl">
            <div class="button <?php echo $item->amount_pieces<=0?'disabled':'' ?>" onclick="pickup_change(this,'-')">-</div>
            <div class="button"  onclick="pickup_change(this,'+')">+</div>
            <div class="button amount <?php echo $item->amount_pieces==$item->amount_order?'disabled':'' ?>"  onclick="pickup_change(this,'<?php echo $item->amount_order ?>')"><span><?php echo $item->amount_order ?></span></div>
          </div>
        <?php elseif($item->price_type == 'k'): ?>
          <div class="amount">
            <b><span class="value"><?php echo format_amount($item->amount_weight) ?></span> <?php echo translate_product_type($item->price_type) ?></b>
          </div>
          <div class="ctrl weight">
            <?php 
              $scale_title = $item->product->name;
              if($item->product->type != 'v'){
                $scale_title .= ' '.format_amount($item->amount_order).' '.translate_product_type($item->product->type);
                $value_exact = $item->amount_order;
                $value_min = round($item->amount_order*0.9, 2);
                $value_max = round($item->amount_order*1.1, 2);
                $price_sum = 0;
              }else{
                $value_exact = $variable_sum;
                $value_min = round($variable_sum*0.9, 2);
                $value_max = round($variable_sum*1.1, 2);
                $price_sum = $variable_sum;
              }
            ?>
            <div class="button scale" onclick="scale_show(this)" data-type="scale" data-title="<?php echo htmlentities($scale_title) ?>" data-value_exact="<?php echo $value_exact ?>" data-value_min="<?php echo $value_min ?>" data-value_max="<?php echo $value_max ?>" data-price="<?php echo $item->price ?>" data-price_sum="<?php echo $price_sum ?>" data-price_sum_pickup="<?php echo $pickup_variable_sum-$item->price_sum ?>">
              <div>
                <i class="fa-solid fa-weight-scale"></i>
              </div>
            </div>
          </div>
        <?php endif ?>
      </div>
    </div>
    <div class="col4 right result">
      <?php
        if($item->price_type == 'p'){
          $amount = $item->amount_pieces;
          $variance = 0;
        }else{
          $amount = $item->amount_weight;
          $variance = 0.1;
        }
        $icon = 'good';
        if($amount < $item->amount_order * (1 - $variance)){
          $icon = 'warning';
        }elseif($amount > $item->amount_order * (1 + $variance)){
          $icon = 'error';
        }
      ?>
      <?php if($item->amount_order): ?>
        <div class="icon <?php echo $icon ?>">
          <?php if($icon != 'good'): ?>
            <i class="fa-solid fa-triangle-exclamation"></i>
          <?php else: ?>
            <i class="fa-solid fa-check"></i>
          <?php endif ?>
        </div>
      <?php else: ?>
        <div class="price_sum">
          <?php if($item->price_sum): ?>
            <?php echo format_money($item->price_sum) ?> EUR
          <?php endif ?>
        </div>
      <?php endif ?>
      <?php /*
      <!--
      <div class="price_sum">
        <?php if($item->price_sum): ?>
          <?php echo format_money($item->price_sum) ?> EUR
        <?php endif ?>
      </div>
      -->
      */ ?>
    </div>
  </div>
<?php endforeach ?>
<?php require('scale.part.php'); ?>


<?php ob_start(); ?>
<div class="row">
  <div class="inner_row mb1">
    <div class="col2"></div>
    <div class="col4">
      <div>Bestellte Produkte</div>
    </div>
    <div class="col4">
      <div>
        <?php echo number_format($order_sum,2,',','') ?>&nbsp;EUR
      </div>
    </div>
    <div class="col6 last right">
      <div class="price_sum">
        <?php echo number_format($pickup_order_sum,2,',','') ?>&nbsp;EUR
      </div>
    </div>
  </div>
  <?php if($variable_sum): ?>
    <div class="inner_row">
      <div class="col2"></div>
      <div class="col4">
        <div>Gemüseanteil</div>
      </div>
      <div class="col4">
        <div>
          <?php echo number_format($variable_sum,2,',','') ?>&nbsp;EUR
        </div>
      </div>
      <div class="col6 last right">
        <div class="price_sum">
          <?php echo number_format($pickup_variable_sum,2,',','') ?>&nbsp;EUR
        </div>
      </div>
    </div>
    <div class="inner_row">
      <div class="col2"></div>
      <div>
        <div>
          <?php if($product_type == 'v'): ?>
            <small style="color:#888;">Noch <?php echo ($others['orders_count']-$others['pickup_count']) ?> weitere (<?php echo ($others['orders_sum']-$others['pickup_sum']) ?> EUR) von inkl. <?php echo ($others['orders_count']+1) ?> Gemüseanteil-Abholern (<?php echo ($others['orders_sum']+$variable_sum) ?> EUR)
            </small>
           <?php else: ?>
            &nbsp;
           <?php endif ?>
        </div>
      </div>
    </div>
  <?php endif ?>
</div>
<?php $PROPERTIES['footer']=ob_get_clean(); ?>

