<?php
$PROPERTIES['pathbar']=array('/settings'=>'Einstellungen');
?>
<div class="selection">
  <?php if(0): ?>
  <div class="item" onclick="location.href='/preferences'">
    <span class="label">Präferenzen</span>
  </div>
  <?php endif ?>
  <div class="item" onclick="location.href='/order'">
    <span class="label">Abholmengen</span>
  </div>
  <?php if(0): ?>
  <div class="item" onclick="location.href='/access'">
    <span class="label">Zugriff</span>
  </div>
  <?php endif ?>
  <?php if(0): ?>
  <div class="item" onclick="location.href='/account'">
    <span class="label">Konto</span>
  </div>
  <?php endif ?>
</div>
