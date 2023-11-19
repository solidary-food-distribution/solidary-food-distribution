<div class="center login">
  <div class="row" id="login_form">
    <div class="inner_row mb1">
      <div class="col3">
        <div>Bitte E-Mail-Adresse angeben, es wird dorthin eine E-Mail mit einem Link zum Setzen eines neuen Passwortes gesendet.</div>
      </div>
    </div>
    <div class="inner_row">
      <div class="col1">
        <div>E-Mail</div>
      </div>
      <div class="col3">
        <div><input type="text" id="email" name="email<?php echo time() ?>" value="<?php echo htmlentities($email) ?>" /></div>
      </div>
    </div>
    <div class="inner_row mt1">
      <div class="col2">
        <div id="password_lost" class="button" onclick="auth_password_lost()">Passwort zurücksetzen</div>
      </div>
      <div class="col2 right last">
        <div id="login" class="button" onclick="location.href='/auth/login?email='+$('#email').val();">Abbrechen</div>
      </div>
    </div>
  </div>
  <br>
  <div id="out" class="row" style="display:none;">
  </div>
</div>
