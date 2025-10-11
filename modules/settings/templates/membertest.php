<?php
$PROPERTIES['pathbar']=array(
  '/settings'=>'Einstellungen',
  ''=>'Patenschaften'
);
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
<div class="controls">
  <div class="button" onclick="if(confirm('Neue Patenschaft anlegen?')){location.href='/settings/membertest_new';}">Neue Patenschaft anlegen</div>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($members as $member): ?>
  <?php
    $last_pickup = '-';
    if(isset($last_pickups[$member->id])){
      $last_pickup = format_date($last_pickups[$member->id]);
    }
  ?>
  <div class="row">
    <div class="inner_row">
      <div class="col9">
        <div>
          <div>Name: <?php echo $member->name ?></div>
        </div>
      </div>
      <div class="col6">
        <div>
          <div>Angelegt: <?php echo format_date($member->created) ?></div>
        </div>
      </div>
    </div>
    <div class="inner_row">
      <div class="col9">
        <div>
          Bestellen bis: <?php echo format_date($member->deactivate_on) ?>
        </div>
      </div>
      <div class="col6">
        <div>
          <div><!--Aktuellste Bestellung: <?php echo $last_order ?>--></div>
        </div>
      </div>
    </div>
    <div class="inner_row">
      <div class="col9">
        <div>
          <!--Bestellgrenze: <?php echo $member->order_limit?format_money($member->order_limit):'-' ?> EUR/Woche-->
        </div>
      </div>
      <div class="col6">
        <div>
          <!--Abholungsumme:-->
        </div>
      </div>
    </div>
    <div class="col1 right last">
      <span class="button" onclick="location.href='/settings/membertest_edit?member_id=<?php echo $member->id ?>';">
        <i class="fa-solid fa-pencil"></i>
      </span>
    </div>
  </div>
<?php endforeach ?>

