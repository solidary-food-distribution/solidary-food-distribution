<?php
$PROPERTIES['pathbar']=array('/settings'=>'Einstellungen', '' => 'Benachrichtigen');
$order_reminders = array(
  'NOTIFICATION_ORDER_REMINDER-24_HOURS' => '24 Stunden vor Bestellschluss',
  'NOTIFICATION_ORDER_REMINDER-12_HOURS' => '12 Stunden vor Bestellschluss',
  'NOTIFICATION_ORDER_REMINDER-6_HOURS' => '6 Stunden vor Bestellschluss',
  'NOTIFICATION_ORDER_REMINDER-3_HOURS' => '3 Stunden vor Bestellschluss',
);
?>
<div class="row">
  <div class="inner_row">
    <div class="col6">
      <div class="title">Erinnerung zum Bestellen</div>
    </div>
  </div>
  <?php foreach($order_reminders as $notification => $label): ?>
    <div class="inner_row">
      <div class="col1">
        <div><input type="checkbox" name="<?php echo htmlentities($notification) ?>" value="1" <?php echo ($notifications[$notification]=='1'?'checked="checked"':'') ?> onchange="notifications_update(this)" /></div>
      </div>
      <div class="col8">
        <div><?php echo htmlentities($label) ?></div>
      </div>
    </div>
  <?php endforeach ?>
</div>