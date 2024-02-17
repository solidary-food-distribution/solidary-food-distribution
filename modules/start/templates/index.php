<div class="selection">
  <?php if(user_has_access('deliveries') || user_has_access('inventory')): ?>
    <div class="item" onclick="location.href='/store'">
      <span class="label">Abholraum</span>
    </div>
  <?php elseif(user_has_access('pickups')): ?>
    <div class="item" onclick="location.href='/pickups'">
      <span class="label">Abholungen</span>
    </div>
  <?php endif ?>
  <div class="item" onclick="location.href='/timesheet'">
    <span class="label">Arbeitszeiten</span>
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
