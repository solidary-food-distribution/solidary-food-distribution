<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration');
?>
<div class="selection">
  <?php if($infos): ?>
    <div class="item" onclick="location.href='/admin/infos'">
      <span class="label">Infos</span>
    </div>
  <?php endif ?>
  <?php if($mails): ?>
    <div class="item" onclick="location.href='/mails'">
      <span class="label">E-Mails</span>
    </div>
  <?php endif ?>
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
    <div class="item" onclick="location.href='/admin/products'">
      <span class="label">Produkte</span>
    </div>
  <?php endif ?>
  <?php if($orders): ?>
    <div class="item" onclick="location.href='/admin/orders'">
      <span class="label">Mitglieder Bestellungen</span>
    </div>
  <?php endif ?>
  <?php if($purchases): ?>
    <div class="item" onclick="location.href='/admin/purchases'">
      <span class="label">Lieferanten Bestellungen</span>
    </div>
  <?php endif ?>
  <?php if($debits): ?>
    <div class="item" onclick="location.href='/debits'">
      <span class="label">Abbuchungen</span>
    </div>
  <?php endif ?>
  <?php if($polls): ?>
    <div class="item" onclick="location.href='/admin/polls'">
      <span class="label">Umfragen</span>
    </div>
  <?php endif ?>
  <?php if($remote): ?>
    <div class="item" onclick="location.href='/remote'">
      <span class="label">Remote SSH</span>
    </div>
  <?php endif ?>
</div>
