<?php
$PROPERTIES['body_class']='footer_h4';
?>

<div class="selection">
  <div class="item" onclick="location.href='/order'">
    <span class="label">Bestellen</span>
  </div>
  <?php if(user_has_access('deliveries') || user_has_access('inventory')): ?>
    <div class="item" onclick="location.href='/start/store'">
      <span class="label">Abholraum</span>
    </div>
  <?php elseif(user_has_access('pickups')): ?>
    <div class="item" onclick="location.href='/pickups'">
      <span class="label">Abholungen</span>
    </div>
  <?php endif ?>
  <div class="item" onclick="location.href='/activities'">
    <span class="label">Aktivitäten</span>
  </div>
  <div class="item" onclick="location.href='/polls'">
    <span class="label">Umfragen</span>
  </div>
  <div class="item" onclick="location.href='/settings'">
    <span class="label">Einstellungen</span>
  </div>
  <?php if(user_has_access('admin')): ?>
    <div class="item" onclick="location.href='/admin'">
      <span class="label">Administration</span>
    </div>
  <?php endif ?>
</div>

<?php ob_start(); ?>
<div class="row center">
  Aktuelle Version: <a href='/start/version'>%VERSION%</a>
</div>
<?php
  $PROPERTIES['footer']=ob_get_clean();
?>
