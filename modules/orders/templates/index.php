<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration','/orders'=>'Abholmengen');
$PROPERTIES['body_class']='footer_h8';
$sum=0;
$sum_su=0;
$taxes=array();
$month_factor=4;
?>

<?php foreach($orders as $product_id=>$product): ?>
  <div class="row product">
    <div class="inner_row mb1">
      <div class="col6">
        <div class="info">
          <div class="name"><?php echo $product['name'] ?></div>
          <div class="producer"><?php echo $product['producer_name'] ?></div>
        </div>
      </div>
      <div class="col2 right">
        <div>
          <?php 
            echo number_format($product['amount'],2,',','');
            if($product['period']=='w'){
              echo '<br><small>'.number_format($product['amount']*$month_factor,2,',','').'</small>';
            }
          ?>
        </div>
      </div>
      <div class="col3">
        <div>
          <?php 
            echo translate_product_type($product['type']).'/'.translate_product_period($product['period']);
            if($product['period']=='w'){
              echo '<br><small>'.translate_product_type($product['type']).'/Monat</small>';
            }
          ?>
        </div>
      </div>
      <div class="col3">
        <div>
          <?php
            if($product['type']!='b'){
              echo 'x&nbsp;'.number_format($product['price'],2,',','').' EUR ';
            }
            $factor=1;
            if($product['period']=='w'){
              $factor=$month_factor;
              echo 'x&nbsp;'.$month_factor.' Wo.';
            }
          ?>
        </div>
      </div>
      <div class="col4 right last">
        <div>
          <?php 
            $rowsum=round($factor*$product['amount']*$product['price'],2);
            echo number_format($rowsum,2,',','');
            $sum+=$rowsum;
          ?> EUR<br>
          <br>
          Erz.:
          <?php
            $rowsum_su=round($factor*$product['amount']*$product['purchase'],2);
            echo ' '.number_format($rowsum_su,2,',','');
          ?> EUR<br>
          +MwSt:
          <?php
            $rowsum_tax=round($factor*$product['amount']*$product['purchase']*$product['tax']/100,2);
            echo ' '.number_format($rowsum_tax,2,',','');
          ?> EUR<br>
          =
          <?php
            echo ' '.number_format($rowsum_su+$rowsum_tax,2,',','');
            $sum_su+=round($rowsum_su+$rowsum_tax,2);
          ?> EUR<br>
        </div>
      </div>
    </div>
    <?php foreach($product['members'] as $member): ?>
      <div class="inner_row member<?php echo $product_id ?>" style="display:none;">
        <div class="col6">
          <div><?php echo $member['name'] ?></div>
        </div>
        <div class="col2 right">
          <div><?php echo number_format($member['amount'],2,',','') ?></div>
        </div>
        <div class="col2">
          <div><?php echo translate_product_type($product['type']).'/'.translate_product_period($product['period']) ?></div>
        </div>
      </div>
    <?php endforeach ?>
    <div class="inner_row">
      <div class="col">
        <div>
          <div class="button" onclick="$('.member<?php echo $product_id ?>').toggle();$('.member<?php echo $product_id ?>b').toggle();">Mitglieder <span class="member<?php echo $product_id ?>b">zeigen</span><span class="member<?php echo $product_id ?>" style="display:none;">verbergen</span></div>
        </div>
      </div>
    </div>
  </div>
<?php endforeach ?>

<?php
  ob_start();
?>
<div class="row">
  <?php /*
  <div class="inner_row mb1">
    <div class="col2 right last">
      <div>
        <?php echo number_format($sum,2,',',''); ?> EUR/Monat
      </div>
    </div>
  </div>
  <?php foreach($taxes as $taxp=>$taxv): ?>
    <div class="inner_row">
      <div class="col3"></div>
      <div class="col2"></div>
      <div class="col3 right">
        <div>Steuer <?php 
          $taxsum=round($taxp*$taxv/100,2);
          $sum+=$taxsum;
          echo round($taxp,2) ?>% auf <?php echo number_format($taxv,2,',','');
        ?></div>
      </div>
      <div class="col2 right last">
        <div>
          <?php echo number_format($taxsum,2,',',''); ?> EUR/Monat
        </div>
      </div>
    </div>
  <?php endforeach*/ ?>
  <div class="inner_row">
    <div class="col4 right last">
      <div>
        In: <?php echo number_format($sum,2,',',''); ?> EUR
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4 right last">
      <div>
        Out: <?php echo number_format($sum_su,2,',',''); ?> EUR
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4 right last">
      <div>
        = <?php echo number_format($sum-$sum_su,2,',',''); ?> EUR
      </div>
    </div>
  </div>
</div>
<?php
  $PROPERTIES['footer']=ob_get_clean();
?>