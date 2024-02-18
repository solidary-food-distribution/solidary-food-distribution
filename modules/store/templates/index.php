<?php
$PROPERTIES['pathbar']=array('/store'=>'Abholraum');
?>
<div class="selection">
  <?php if(user_has_access('pickups')): ?>
    <div class="item" onclick="location.href='/pickups'">
      <span class="label">Abholungen</span>
    </div>
  <?php endif ?>
  <?php if(user_has_access('deliveries')): ?>
    <div class="item" onclick="location.href='/deliveries'">
      <span class="label">Lieferungen</span>
    </div>
  <?php endif ?>
  <?php if(user_has_access('inventory')): ?>
    <div class="item" onclick="location.href='/inventory'">
      <span class="label">Inventur</span>
    </div>
  <?php endif ?>
</div>