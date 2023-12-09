<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration','/debits'=>'Abbuchungen');
$PROPERTIES['body_class']='footer_h4';
$totalsum=0;
?>
<?php foreach($members as $member_id=>$member): ?>
  <div class="row">
<?php
  ob_start();
  $sum=0;
?>
  <?php foreach($member['products'] as $pid=>$product): ?>
    <div class="inner_row product<?php echo $member_id ?>" style="display:none;">
      <div class="col8">
        <div><?php echo $product['name'] ?></div>
      </div>
      <div class="col6">
        <div>
          <?php
            echo str_replace('.',',',round($product['amount'],2));
            $rowsum=$product['amount'];
            if($product['type']!='b'){
              echo ' x '.number_format($product['price'],2,',','');
              $rowsum*=$product['price'];
            }
            if($product['period']=='w'){
              echo ' x 52/12';
              $rowsum=round($rowsum*52/12,2);
            }
            if(!$product['tax_incl']){
              echo ' + '.str_replace('.',',',round($product['tax'],2)).'% MwSt';
              $rowsum=round($rowsum*(100+$product['tax'])/100,2);
            }
            $sum+=$rowsum;
          ?>
        </div>
      </div>
      <div class="col4 right last">
        <div><?php echo number_format($rowsum,2,',','') ?> EUR/Monat</div>
      </div>
    </div>
  <?php endforeach ?>
<?php
  $products=ob_get_clean();
?>

    <div class="inner_row mb1">
      <div class="col8">
        <div>
          <div><?php echo $member['name'] ?> (<?php echo $member['identification'] ?>)</div>
        </div>
      </div>
      <div class="col4 right last">
        <div>
          <div><?php echo number_format($sum,2,',','') ?> EUR/Monat</div>
        </div>
      </div>
    </div>
    <?php echo $products ?>
    <div class="inner_row">
      <div class="col">
        <div>
          <div class="button" onclick="$('.product<?php echo $member_id ?>').toggle();$('.product<?php echo $member_id ?>b').toggle();">Positionen <span class="product<?php echo $member_id ?>b">zeigen</span><span class="product<?php echo $member_id ?>" style="display:none;">verbergen</span></div>
        </div>
      </div>
    </div>
  </div>
  <?php $totalsum+=$sum; ?>
<?php endforeach ?>
<?php
  ob_start();
?>
<div class="row">
  <div class="inner_row">
    <div class="col6 right last">
      <div>
        <?php echo number_format($totalsum,2,',',''); ?> EUR/Monat
      </div>
    </div>
  </div>
</div>
<?php
  $PROPERTIES['footer']=ob_get_clean();
?>