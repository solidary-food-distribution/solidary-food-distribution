<?php
$PROPERTIES['pathbar']=array('/settings'=>'Einstellungen');
?>
<div class="selection">
  <!--
  <div class="item" onclick="location.href='/preferences'">
    <span class="label">Präferenzen</span>
  </div>
  <div class="item" onclick="location.href='/order'">
    <span class="label">Abholmengen</span>
  </div>
-->
  <?php if(0): ?>
  <div class="item" onclick="location.href='/access'">
    <span class="label">Zugriff</span>
  </div>
  <?php endif ?>
  <div class="item" onclick="location.href='/user'">
    <span class="label">Zugangsdaten</span>
  </div>
  <?php if($membertest): ?>
    <div class="item" onclick="location.href='/settings/membertest'">
      <span class="label">Patenschaften</span>
    </div>
  <?php endif ?>
</div>
