<?php
$PROPERTIES['body_class']='footer_h4';
?>

<?php if(!empty($message)): ?>
  <div class="row">
    <?php echo htmlentities($message) ?>
  </div>
<?php endif ?>

<?php if(!$deactivated): ?>

<?php foreach($infos as $info): ?>
  <div class="row" id="info<?php echo $info->id ?>">
    <div class="inner_row">
      <div class="col4">
        <?php if($info->published == '0000-00-00 00:00:00'): ?>
          Entwurf<br>
          <?php echo format_date($info->created) ?>
        <?php else: ?>
          <b><?php echo format_date($info->published) ?></b>
        <?php endif ?>
      </div>
      <div class="col13">
        <b><?php echo htmlentities($info->subject) ?></b>
      </div>
    </div>
    <div class="inner_row">
      <div class="col4"></div>
      <div class="col13 mt0_5">
        <div>
          <?php echo format_content($info->content) ?>
        </div>
      </div>
      <div class="col1 right last" style="position:relative">
        <span class="button" style="position:absolute;bottom:0px;" onclick="start_info_read(<?php echo $info->id ?>);">
          <i class="fa-solid fa-check"></i>
        </span>
      </div>
    </div>
  </div>
<?php endforeach ?>


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
  <div class="item" onclick="location.href='/forum'">
    <span class="label">Forum</span>
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

<?php endif ?>

<?php ob_start(); ?>
<div class="row center">
  Version: <a href='/start/version'>%VERSION%</a>
</div>
<?php
  $PROPERTIES['footer']=ob_get_clean();
?>
