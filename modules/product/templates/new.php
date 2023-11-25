<?php
if(isset($delivery)){
  $PROPERTIES['pathbar']=array('/deliveries'=>'Lieferungen','/delivery?id='.$delivery['id']=>format_date($delivery['created'],'j.n.Y H:i').' '.$delivery['supplier_name'], '/delivery/new_item?delivery_id=1'=>'Neue Position', ''=>'Neues Produkt');
}else{
  $PROPERTIES['pathbar']=array('/admin'=>'Administration','/products'=>'Produkte',''=>'Neues Produkt');
}
?>

<div class="row">
  
</div>