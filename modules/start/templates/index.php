<div class="selection">
  <?php if(user_has_access('deliveries')): ?>
    <div class="item" onclick="location.href='/deliveries'">
      <span class="label">Lieferung</span>
    </div>
  <?php endif ?>
  <?php if(0): ?>
  <div class="item" onclick="location.href='/pickups'">
    <span class="label">Abholung</span>
  </div>
  <?php endif ?>
  <div class="item" onclick="location.href='/activities'">
    <span class="label">Aktivitäten</span>
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
