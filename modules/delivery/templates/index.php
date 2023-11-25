<?php
$PROPERTIES['pathbar']=array('/deliveries'=>'Lieferungen','/delivery?id='.$delivery->id => format_date($delivery->created,'j.n.Y H:i').' '.$delivery->supplier->name);
?>

<?php ob_start(); ?>
  <div class="controls">
    <div class="control">
      <span class="label" onclick="location.href='/delivery/edit?id=<?php echo $delivery->id ?>';">Bearbeiten</span>
    </div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php 
if($edit){
  require('head_edit.part.php');
}else{
  require('head.part.php'); 
}
?>

<?php 
  foreach($delivery->items as $di_id=>$item){
    if($item_id==$di_id){
      require('item_edit.part.php');
    }else{
      require('item.part.php');
    }
  }
?>

<div class="main_button button" onclick="alert('NOCH NICHT IMPLEMENTIERT');return false;location.href='/delivery/products?delivery_id=<?php echo $delivery->id ?>';">Neue Position anlegen</div>