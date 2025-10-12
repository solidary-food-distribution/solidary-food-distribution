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
  <div class="button help"><a href="/help/Patenschaften.pdf" target="_blank"><i class="fa-solid fa-question"></i></a></div>
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
          Name: <?php echo $member->name ?>
        </div>
      </div>
      <div class="col7">
        <div>
          Bestellen bis: <?php echo format_date($member->deactivate_on) ?>
        </div>
      </div>
    </div>
    <div class="inner_row">
      <div class="col9">
        <div>
          Angelegt: <?php echo format_date($member->created) ?>
        </div>
      </div>
      <div class="col8">
        <div>
          Bestellgrenze: <?php echo $member->order_limit?format_money($member->order_limit):'-' ?> EUR/Woche
        </div>
      </div>
      <div class="col1 right last">
        <span class="button" onclick="location.href='/settings/membertest_edit?member_id=<?php echo $member->id ?>';">
          <i class="fa-solid fa-pencil"></i>
        </span>
      </div>
    </div>
    <?php
      $history = $member_history[$member->id];
    ?>
    <?php if(!isset($history)): ?>
      <div class="inner_row">
        Noch keine Bestellungen/Abholungen
      </div>
    <?php else: ?>
      <?php
        ksort($history);
      ?>
      <div class="inner_row">
        <div class="col4">Datum</div>
        <div class="col3 right">Bestellung</div>
        <div class="col5 right">Abholung/Abrechnung</div>
        <!--<div class="col3 right">Abgerechnet</div>-->
      </div>
      <?php foreach($history as $hdate => $h): ?>
        <div class="inner_row">
          <div class="col4">
            <?php echo format_date($hdate); ?>
          </div>
          <div class="col3 right">
            <?php if(isset($h['order'])): ?>
              <?php echo format_money($h['order']['price_sum']).' EUR' ?>
            <?php endif ?>
          </div>
          <div class="col5 right">
            <?php if(isset($h['pickup'])): ?>
              <?php echo format_money($h['pickup']['price_sum']).' EUR' ?>
            <?php endif ?>
          </div>
        </div>
      <?php endforeach ?>
    <?php endif ?>
  </div>
<?php endforeach ?>

