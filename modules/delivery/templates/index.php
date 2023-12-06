<?php
$PROPERTIES['pathbar']=array('/deliveries'=>'Lieferungen','/delivery?delivery_id='.$delivery->id => format_date($delivery->created,'j.n.Y').' '.$delivery->supplier->name);
?>

<div class="row bottom_no_radius" id="delivery_head">
  <div class="col6">
    <div>
      <b><?php echo format_date($delivery->created,'j.n.Y') ?></b> 
      <?php echo format_date($delivery->created,'H:i', false) ?>
      <br>
      <span class="smaller"><?php echo $delivery->creator->name ?></span>
    </div>
  </div>
  <div class="col4">
    <div><b><?php echo $delivery->supplier->name ?></b></div>
  </div>
  <div class="col4"></div>
  <div class="col3 right">
    <div><?php echo number_format($delivery->price_total,2,',','') ?> EUR</div>
  </div>
  <div class="col1 right last">
    <div class="buttons">
      <span class="button" onclick="location.href='/delivery/edit?delivery_id=<?php echo $delivery->id ?>'">
        <i class="fa-solid fa-pencil"></i>
      </span>
    </div>
  </div>
</div>

<?php 
  foreach($delivery->items as $di_id=>$item){
    require('item.part.php');
  }
?>

<div class="main_button button" onclick="location.href='/delivery/products?delivery_id=<?php echo $delivery->id ?>';">Neue Position anlegen</div>