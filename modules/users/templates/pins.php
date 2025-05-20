<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration','/users'=>'Benutzer',''=>'Türcodes');
?>


<?php foreach($users as $user): ?>
  <div class="row">
    <div class="col5">
      <?php echo $user['created'] ?>
    </div>
    <div class="col2">
      <?php echo $user['pickup_pin'] ?>
    </div>
    <div class="col8">
      <?php echo htmlentities($user['name']) ?>
    </div>
  </div>
<?php endforeach ?>