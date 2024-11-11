<?php
$PROPERTIES['pathbar']=array('/activities'=>'Aktivitäten',''=>'Aufgaben');
$PROPERTIES['body_class']='header_h5';?>

<?php ob_start(); ?>
  <div class="controls">

  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>
