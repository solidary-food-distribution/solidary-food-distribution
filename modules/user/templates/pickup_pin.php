<?php
$PROPERTIES['pathbar']=array('/settings'=>'Einstellungen','/user'=>'Zugangsdaten', '' => 'Login für Waage');
require_once('pin.include.php');
?>

<script type="text/javascript">
  var PIN_ICONS=[
    <?php echo "'".implode("', '", PIN_ICONS)."'" ?>
  ];
</script>

<div class="row">
  <div class="inner_row">
    <div>
      <div class="title">Abholraum - Login für Waage</div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4">
      <div>Login für Waage</div>
    </div>
    <div class="col12">
      <div class="keyboard_keys icons smaller">
        <div id="pickup_pin">
          <?php for($pini = 0; $pini < 6; $pini++): ?>
            <div class="mr0_2"></div>
          <?php endfor ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
  $pin_title = 'Bitte 3 bis 6 Zeichen auswählen. Reihenfolge ist relevant!';
  require('pin.part.php'); 
?>

<script type="text/javascript">
  <?php $user_pickup_pin = implode(',', str_split($pickup_pin,2)); ?>
  var user_pickup_pin = [<?php echo $user_pickup_pin ?>];
  user_show_pickup_pin();
</script>