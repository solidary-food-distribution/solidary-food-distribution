<?php
$PROPERTIES['pathbar']=array('/pickups'=>'Abholungen');
$PROPERTIES['body_class']='header_h5';
?>


<?php ob_start(); ?>
  <div class="controls">
    <div class="control">
      <span class="label" onclick="location.href='/pickup/new'">Neue Abholung</span>
    </div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

NOCH NICHT UMGESETZT<br>
<br>
Abholungen durchführen