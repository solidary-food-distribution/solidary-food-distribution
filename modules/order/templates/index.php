<?php
$PROPERTIES['pathbar']=array('/settings'=>'Einstellungen',''=>'Abholmengen');
$PROPERTIES['body_class']='footer_h8';
$sum_price_sum=0.0;
$sum_purchase=0;
$sum_supplier=0;
$sum_geno_fa=0;
$sum_geno=0;
$base_cost=0; //28.0;
?>
<?php foreach($orders as $op): ?>
<?php
  logger(print_r($op,1));
  $name=$op->product->name;
  $producer_name=$op->product->producer->name;
  $amount=$op->get_amount_formated();

  $unit=translate_product_type($op->product->type);
  $unit.='&nbsp;/&nbsp;'.translate_product_period($op->product->period);

  $type=translate_product_type($op->product->type);
  $attr_less='onclick="order_change(this,\'-\')"';
  $class_less='';
  if($op->amount=='0.00' || $op->is_locked()){
    $attr_less='';
    $class_less='disabled';

  }
  $attr_more='onclick="order_change(this,\'+\')"';
  $class_more='';
  if(floatval($op->amount)>=floatval($op->product->amount_max) || $op->is_locked()){
    $attr_more='';
    $class_more='disabled';
  }

  $price_detail='';
  $price_detail_class='';
  $price_sum=floatval($op->product->price)*floatval($op->amount);
  $purchase_sum=floatval($op->product->purchase)*floatval($op->amount);
  if($op->amount=='0.00'){
    if($op->product->type!='b'){
      $price_detail=format_money($op->product->price).'&nbsp;EUR<br>/&nbsp;'.$type;
      $price_detail_class='grey';
    }
  }elseif($op->product->type!='b'){
    $price_detail='x&nbsp;'.format_money($op->product->price).'&nbsp;EUR';
  }
  if($op->amount!='0.00' && $op->product->period!='m'){
    $price_detail.=($price_detail==''?'':'<br>').'x 52&nbsp;Wochen<br>/ 12&nbsp;Monate';
    $price_sum*=52/12;
    $purchase_sum*=52/12;
  }
  if(floatval($price_sum)){
    $sum_price_sum+=round($price_sum,2);
  }
?>
  <div class="row product" data-product_id="<?php echo $op->product->id ?>">
    <div class="inner_row">
      <div class="col2">
        <div class="image">
          <!--<img src="" />-->
        </div>
      </div>
      <div class="col6">
        <div class="info">
          <div class="name">
            <b><?php echo $name ?></b>
          </div>
          <div class="producer">
            <?php echo $producer_name ?>
          </div>
        </div>
      </div>
      <div class="col4">
        <div class="amount_ctrl">
          <div class="amount">
            <b><?php echo $amount.'&nbsp;'.$unit ?></b>
          </div>
          <div class="ctrl" data-amount="<?php echo $op->amount ?>">
            <div class="button <?php echo $class_less ?>" <?php echo $attr_less ?> >-</div>
            <div class="button <?php echo $class_more ?>" <?php echo $attr_more ?> >+</div>
          </div>
        </div>
      </div>
      <div class="col6 right last">
        <div class="price_detail <?php echo $price_detail_class ?>">
          <?php echo $price_detail ?>
        </div>
        <div class="price_sum">
          <?php echo $price_sum?format_money($price_sum).' EUR':''; ?>
        </div>
      </div>
    </div>
    <div class="inner_row <?php echo $price_detail_class ?>">
      <div class="col6 right last">
        <div class="price_detail">
          Erzeuger erhält
        </div>
        <div class="price_detail">
          <?php
            $supplier_tax = $purchase_sum*($op->product->tax/100);
            $supplier_sum = $purchase_sum+$supplier_tax;
            $sum_supplier += $supplier_sum;
            echo format_money($supplier_sum);
          ?> EUR
        </div>
      </div>
    </div>
    <div class="inner_row <?php echo $price_detail_class ?>">
      <div class="col6 right last">
        <div class="price_detail">
          Geno -&gt; FA
        </div>
        <div class="price_detail">
          <?php
            $fa_sum = $price_sum - $price_sum/(($op->product->tax+100)/100) - $supplier_tax;
            $sum_geno_fa += $fa_sum;
            echo format_money($fa_sum);
          ?> EUR
        </div>
      </div>
    </div>
    <div class="inner_row <?php echo $price_detail_class ?>">
      <div class="col6 right last">
        <div class="price_detail">
          Geno erhält
        </div>
        <div class="price_detail">
          <?php
            $geno_sum =  $price_sum-$supplier_sum-$fa_sum;
            $sum_geno += $geno_sum;
            echo format_money($geno_sum);
          ?> EUR
        </div>
      </div>
    </div>
  </div>
<?php endforeach ?>

<?php
  ob_start();
?>
<div class="row">
  <?php /*foreach($taxes as $tax=>$tax_value): ?>
    <div class="inner_row">
      <div class="col1"></div>
      <div class="col2">
        <div>Steuer <?php echo str_replace('.',',',rtrim(rtrim($tax,'0'),'.')) ?>%</div>
      </div>
      <div class="col1">
        <div>x&nbsp;<?php echo number_format($tax_value,2,',','') ?>&nbsp;EUR</div>
      </div>
      <div class="col3 last">
        <div class="price_detail"></div>
        <div class="price_sum">
          <?php echo number_format($tax*$tax_value/100,2,',','') ?>&nbsp;EUR
          <?php $sum_price_sum+=round($tax*$tax_value/100,2); ?>
        </div>
      </div>
    </div>
  <?php endforeach*/ ?>
  <?php /*
  <div class="inner_row mb1">
    <div class="col2"></div>
    <div class="col4">
      <div>Grundbeitrag</div>
    </div>
    <div class="col6 last">
      <div class="price_detail"></div>
      <div class="price_sum">
        <?php echo number_format($base_cost,2,',','') ?>&nbsp;EUR
      </div>
    </div>
  </div>
  */ ?>
  <div class="inner_row">
    <div class="col2"></div>
    <div class="col4">
      <div><b>Monatsbetrag</b></div>
    </div>
    <div class="col6 last">
      <div class="price_detail"></div>
      <div class="price_sum">
        <b><?php echo number_format($sum_price_sum+$base_cost,2,',','') ?>&nbsp;EUR</b>
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col6 last">
      <div class="price_detail">Erzeuger erhält</div>
      <div class="price_detail right">
        <?php echo format_money($sum_supplier); ?>&nbsp;EUR
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col6 last">
      <div class="price_detail">Geno->FA</div>
      <div class="price_detail right">
        <?php echo format_money($sum_geno_fa); ?>&nbsp;EUR
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col6 last">
      <div class="price_detail">Geno erhält</div>
      <div class="price_detail right">
        <?php echo format_money($sum_price_sum-$sum_supplier-$sum_geno_fa); ?>&nbsp;EUR
      </div>
    </div>
  </div>
</div>
<?php
  $PROPERTIES['footer']=ob_get_clean();
?>