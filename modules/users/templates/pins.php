<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration','/users'=>'Benutzer',''=>'Türcodes');
?>


<?php foreach($users as $user): ?>
  <div class="row">
    <div class="col3">
      <?php echo substr($user['created'], 0, 10) ?>
    </div>
    <div class="col2">
      <?php echo $user['pickup_pin'] ?>
    </div>
    <div class="col10">
      <?php echo htmlentities($user['name']) ?>
      <?php if($user['name'] != $user['um_name']): ?>
        <?php echo htmlentities('('.$user['um_name'].')') ?>
      <?php endif ?>
    </div>
  </div>
<?php endforeach ?>