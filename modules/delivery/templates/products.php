<?php
$PROPERTIES['pathbar']=array(
  '/deliveries' => 'Lieferungen',
  '/delivery?delivery_id='.$delivery->id => format_date($delivery->created,'j.n.Y').' '.$delivery->supplier->name,
  '' => ($item_id?'Produkt wählen':'Neue Position') 
);
$PROPERTIES['body_class']='header_h5';
?>

<?php /*ob_start(); ?>
  <div class="controls">
    <div class="control filter search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input class="filter" type="text" />
    </div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean();*/ ?>

<div class="selection">
  <?php $type=''; $type_v=false; ?>
  <?php foreach($products as $product): ?>
    <?php if($product->type!=$type && $type!=''): ?>
      <div class="break"></div>
    <?php endif ?>
    <?php 
      $type=$product->type;
      if($type == 'v'){
        $type_v = true;
      }
      $selected='';
      if($item_id && isset($delivery->items[$item_id]) && $delivery->items[$item_id]->product->id == $product->id){
        $selected='selected';
      }
    ?>
    <div class="item <?php echo $selected ?>" onclick="location.href='/delivery/product_select?delivery_id=<?php echo $delivery->id.($item_id?'&item_id='.$item_id:'').'&product_id='.$product->id ?>'">
      <div class="image">
        <!--<img src="" />-->
      </div>
      <div class="info">
        <div class="label">
          <?php echo $product->name ?>
        </div>
        <?php if($product->producer->id != $delivery->supplier->id): ?>
          <div class="producer">
            <?php echo $product->producer->name ?>
          </div>
        <?php endif ?>
      </div>
    </div>
  <?php endforeach ?>
</div>

<?php if($type_v): ?>
  <div class="button main_button" onclick="location.href='/product/new?delivery_id=<?php echo $delivery->id.($item_id?'&item_id='.$item_id:'') ?>'">Neues Produkt aufnehmen</div>
<?php endif ?>
<?php
  $cancel_url = '/delivery?delivery_id='.$delivery->id;
  if($item_id){
    $cancel_url = '/delivery/item_edit?delivery_id='.$delivery->id.'&item_id='.$item_id;
  }
?>
<div class="button main_button" onclick="location.href='<?php echo $cancel_url ?>'">Abbrechen</div>

