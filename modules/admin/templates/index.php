<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration');
?>
<div class="selection">
  <?php if($members): ?>
    <div class="item" onclick="location.href='/members'">
      <span class="label">Mitglieder</span>
    </div>
  <?php endif ?>
  <?php if($users): ?>
    <div class="item" onclick="location.href='/users'">
      <span class="label">Benutzer</span>
    </div>
  <?php endif ?>
  <?php if($products): ?>
    <div class="item" onclick="location.href='/products'">
      <span class="label">Produkte</span>
    </div>
  <?php endif ?>
  <?php if($orders): ?>
    <div class="item" onclick="location.href='/orders'">
      <span class="label">Abholmengen</span>
    </div>
  <?php endif ?>
  <?php if($debits): ?>
    <div class="item" onclick="location.href='/debits'">
      <span class="label">Abbuchungen</span>
    </div>
  <?php endif ?>
</div>
