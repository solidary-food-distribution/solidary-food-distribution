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
  <div class="col4">
    Neues Produkt:
  </div>
  <div class="col6">
    <div class="input string" onclick="input_onfocus(this)" data-info="Produktname" data-url="/product/create_ajax?delivery_id=<?php echo $delivery->id ?>&item_id=<?php echo $item_id ?>" data-field="name" data-type="string" data-regexp="[A-ZÄÖÜ][a-zäöüß].*" data-regexp_fail="Mindestens 2 Buchstaben, der erste groß, der zweite klein." ></div>
  </div>
  <div class="col1 right last">
    <div class="buttons">
      <div class="button ok" onclick="active_input_post_value()">
        <i class="fa-solid fa-check"></i>
      </div>
      <br>
      <div class="button trash mt0_5" onclick="location.href='/delivery/products?delivery_id=<?php echo $delivery->id ?>';">
        <i class="fa-solid fa-xmark"></i>
      </div>
    </div>
  </div>
</div>

<?php require('keyboard.part.php'); ?>
<script type="text/javascript">
  $('.input[data-field]').first().click();
</script>