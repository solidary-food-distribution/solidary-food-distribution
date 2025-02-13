<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  ''=>'Produkte',
);
$PROPERTIES['body_class']='';
?>

<div class="selection">
  <?php foreach($suppliers as $supplier): ?>
    <div class="item" onclick="location.href='/admin/products?supplier_id=<?php echo $supplier->id ?>&status=o'">
      <span class="label"><?php echo htmlentities($supplier->name) ?></span>
    </div>
  <?php endforeach ?>
</div>