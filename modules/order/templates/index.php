<?php
$PROPERTIES['pathbar']=array('/settings'=>'Einstellungen',''=>'Abholmengen');
$PROPERTIES['body_class']='footer_h8';
$sum_price_sum=0.0;
$taxes=array();
$base_cost=28.0;
?>
<?php foreach($order->get_products() as $pid=>$op): ?>
<?php
  $name=$op->name;
  $producer_name=$op->producer_name;
  $amount=$op->get_amount_formated();

  $unit=translate_product_type($op->type);
  $unit.='&nbsp;/&nbsp;'.translate_product_period($op->period);

  $type=translate_product_type($op->type);
  $attr_less='onclick="order_change(this,\'-\')"';
  $class_less='';
  if($op->amount=='0.00' || $op->is_locked()){
    $attr_less='';
    $class_less='disabled';

  }
  $attr_more='onclick="order_change(this,\'+\')"';
  $class_more='';
  if(floatval($op->amount)>=floatval($op->amount_max) || $op->is_locked()){
    $attr_more='';
    $class_more='disabled';
  }

  $price_detail='';
  $price_detail_class='';
  $price_sum=floatval($op->price)*floatval($op->amount);
  if($op->amount=='0.00'){
    if($op->type!='b'){
      $price_detail=str_replace('.',',',$op->price).'&nbsp;EUR<br>/&nbsp;'.$type;
      $price_detail_class='grey';
    }
  }elseif($op->type!='b'){
    $price_detail='x&nbsp;'.str_replace('.',',',$op->price).'&nbsp;EUR';
  }
  if($op->amount!='0.00' && $op->period!='m'){
    $price_detail.=($price_detail==''?'':'<br>').'x 52&nbsp;Wochen<br>/ 12&nbsp;Monate';
    $price_sum*=52/12;
  }
  if($price_sum){
    if($op->tax_incl){
      $price_detail.=($price_detail==''?'':'<br>').'('.str_replace('.',',',round($op->tax,2)).'% MwSt inkl)';
    }else{
      $price_detail.=($price_detail==''?'':'<br>').'+ '.str_replace('.',',',round($op->tax,2)).'% MwSt';
    }
  }
  if(floatval($price_sum)){
    //$taxes[$op->tax]=$taxes[$op->tax]+round($price_sum,2);
    if(!$op->tax_incl){
      $price_sum=round($price_sum*(100+$op->tax)/100,2);
    }
    $sum_price_sum+=round($price_sum,2);
    $price_sum=number_format($price_sum,2,',','').' EUR';
  }else{
    $price_sum='';
  }
?>
<div class="row product" data-product_id="<?php echo $pid ?>">
  <?php require('product_image_info.php'); ?>
  <div class="col2">
    <div class="amount_ctrl">
      <div class="amount">
        <?php echo $amount.'&nbsp;'.$unit ?>
      </div>
      <div class="ctrl" data-amount="<?php echo $op->amount ?>">
        <div class="button <?php echo $class_less ?>" <?php echo $attr_less ?> >-</div>
        <div class="button <?php echo $class_more ?>" <?php echo $attr_more ?> >+</div>
      </div>
    </div>
  </div>
  <div class="col3 last">
    <div class="price_detail <?php echo $price_detail_class ?>">
      <?php echo $price_detail ?>
    </div>
    <div class="price_sum">
      <?php echo $price_sum ?>
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
  <div class="inner_row mb1">
    <div class="col1"></div>
    <div class="col2">
      <div>Grundbeitrag</div>
    </div>
    <div class="col1">
    </div>
    <div class="col3 last">
      <div class="price_detail"></div>
      <div class="price_sum">
        <?php echo number_format($base_cost,2,',','') ?>&nbsp;EUR
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col1"></div>
    <div class="col2">
      <div><b>Monatsbetrag</b></div>
    </div>
    <div class="col1">
    </div>
    <div class="col3 last">
      <div class="price_detail"></div>
      <div class="price_sum">
        <b><?php echo number_format($sum_price_sum+$base_cost,2,',','') ?>&nbsp;EUR</b>
      </div>
    </div>
  </div>
</div>
<?php
  $PROPERTIES['footer']=ob_get_clean();
?>