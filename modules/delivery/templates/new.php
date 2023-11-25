<?php
$PROPERTIES['pathbar']=array('/deliveries'=>'Lieferungen',''=>'Neue Lieferung');
?>
<div class="selection">
  <?php foreach($suppliers as $supplier): ?>
    <div class="item" onclick="location.href='/delivery/new_create?supplier_id=<?php echo $supplier->id ?>'">
      <div class="image">
        <!--<img src="" />-->
      </div>
      <div class="info">
        <div class="name">
          <?php echo $supplier->name ?>
        </div>
      </div>
    </div>
  <?php endforeach ?>
</div>

<div class="button main_button" onclick="location.href='/deliveries'">Abbrechen</div>
