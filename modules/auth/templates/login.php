<div class="center login">
  <?php if($_SESSION['scale']): ?>
    <?php require_once('pin.include.php'); ?>
    <div class="row" id="pin_form">
      <div class="inner_row">
        <div>
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
      $pin_title = 'Gewählte Zeichen in Reihenfolge eingeben';
      $noshuffle = 1;
      require('pin.part.php'); 
    ?>
    <script type="text/javascript">
      var PIN_ICONS=[
        <?php echo "'".implode("', '", PIN_ICONS)."'" ?>
      ];
      user_show_pickup_pin();
    </script>
    <br>
    <div class="button large" style="width:auto;position:absolute;bottom:0em;left:0em;" onclick="auth_shutdown()">Herunterfahren</div>
  <?php else: ?>
    <div class="row" id="login_form">
      <div class="inner_row">
        <div class="col2">
          <div>E-Mail</div>
        </div>
        <div class="col6">
          <div><input type="text" id="email" name="email<?php echo time() ?>" onkeyup="auth_may_login(event)" value="<?php echo htmlentities($email) ?>" /></div>
        </div>
      </div>
      <div class="inner_row">
        <div class="col2">
          <div>Passwort</div>
        </div>
        <div class="col6">
          <div><input type="password" id="password" onkeyup="auth_may_login(event)" /></div>
        </div>
      </div>
      <div class="inner_row mt1">
        <div class="col6">
          <div id="password_lost" class="button" onclick="location.href='/auth/password_lost?email='+$('#email').val()">Passwort vergessen</div>
        </div>
        <div class="col2 right last">
          <input type="hidden" id="_forward" value="<?php echo htmlentities($_forward) ?>" />
          <input type="hidden" id="_query" value="<?php echo htmlentities($_query) ?>" />
          <div id="login" class="button" onclick="auth_login()">Login</div>
        </div>
      </div>
    </div>
    <br>
    <div id="out" class="row" style="display:none;">
    </div>
  <?php endif ?>
</div>