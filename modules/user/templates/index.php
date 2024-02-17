<?php
$PROPERTIES['pathbar']=array('/settings'=>'Einstellungen',''=>'Zugangsdaten');
?>

<div class="row">
  <div class="inner_row">
    <div class="col6">
      <div class="title">Name / Zugangsdaten</div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4">
      <div>Name</div>
    </div>
    <div class="col4">
      <div><input type="text" name="name" value="<?php echo htmlentities($user['name']) ?>" onchange="user_update(this)" disabled="disabled"></div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4">
      <div>E-Mail</div>
    </div>
    <div class="col4">
      <div><input type="text" name="email" value="<?php echo htmlentities($user['email']) ?>" onchange="user_update(this)" disabled="disabled"></div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4">
      <div>Passwort</div>
    </div>
    <div class="col4">
      <div>
        <div class="button" onclick="location.href='/auth/password_lost?email=<?php echo urlencode($user['email']) ?>&cancel=/user';">Passwort ändern</div>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="inner_row">
    <div class="col6">
      <div class="title">Abholraum</div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4">
      <div>Login für Waage</div>
    </div>
    <div class="col4">
      <div>
        <div class="button" onclick="location.href='/user/pickup_pin';">PIN <?php echo empty($pickup_pin)?'setzen':'ändern' ?></div>
      </div>
    </div>
  </div>
</div>
