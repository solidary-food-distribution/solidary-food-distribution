<?php
if(isset($delivery)){
  $PROPERTIES['pathbar']=array(
    '/deliveries' => 'Lieferungen',
    '/delivery?delivery_id='.$delivery->id.'&item_id='.$item_id => format_date($delivery->created,'j.n.Y').' '.$delivery->supplier->name,
    '/delivery/products?delivery_id='.$delivery->id => 'Neue Position',
    ''=>'Neues Produkt'
  );
}else{
  $PROPERTIES['pathbar']=array('/admin'=>'Administration','/products'=>'Produkte',''=>'Neues Produkt');
}
?>

<div class="row">
  Neues Produkt:<br> 
  <div class="input string" onclick="input_onfocus(this)" data-info="Produktname" data-url="/product/create_ajax?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item_id ?>" data-field="name" data-type="string" ></div>
</div>

<?php require('keyboard.part.php'); ?>
<script type="text/javascript">
  $('.input[data-field]').first().click();
</script>