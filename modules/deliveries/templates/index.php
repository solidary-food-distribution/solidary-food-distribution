<?php
$PROPERTIES['pathbar']=array('/deliveries'=>'Lieferungen');
$PROPERTIES['body_class']='header_h5';
?>


<?php ob_start(); ?>
  <div class="controls">
    <div class="control">
      <span class="label" onclick="location.href='/delivery/new'">Neue Lieferung</span>
    </div>
    <div class="control">
      <span class="label" onclick="location.href='/product/fresh_new'">Neues Frische-Produkt</span>
    </div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>


NOCH NICHT UMGESETZT<br>
<br>
Warenlieferung aufnehmen