<?php /*
<!--
<div style="font-size:500%;text-align: center;">
<table><tr>
<td><i class="fas fa-sun"></i></td>
<td><i class="far fa-moon"></i></td>
<td><i class="fas fa-bolt"></i></td>
<td><i class="far fa-snowflake"></i></td>
</tr><tr>
<td><i class="fas fa-tint"></i></td>
<td><i class="fas fa-cloud-rain"></i></td>
<td><i class="fas fa-seedling"></i></td>
<td><i class="fas fa-tree"></i></td>
</tr><tr>
<td><i class="fas fa-fish"></i></td>
<td><i class="fas fa-cat"></i></td>
<td><i class="fas fa-horse"></i></td>
<td><i class="fas fa-crow"></i></td>
</tr><tr>
<td><i class="fas fa-apple-alt"></i></td>
<td><i class="fas fa-carrot"></i></td>
<td><i class="fas fa-pepper-hot"></i></td>
<td><i class="far fa-lemon"></i></td>
</tr><tr>
<td><i class="fas fa-coffee"></i></td>
<td><i class="fas fa-beer"></i></td>
<td><i class="fas fa-store-alt"></i></td>
<td><i class="fas fa-couch"></i></td>
</tr><tr>
<td><i class="fas fa-tractor"></i></td>
<td><i class="fas fa-bicycle"></i></td>
<td><i class="far fa-paper-plane"></i></td>
<td><i class="fas fa-anchor"></i></td>
</tr><tr>
<td><i class="far fa-heart"></i></td>
<td><i class="far fa-smile"></i></td>
<td><i class="fas fa-music"></i></td>
<td><i class="fas fa-key"></i></td>
</tr><tr>
<td><i class="fas fa-hourglass-half"></i></td>
<td><i class="fas fa-umbrella"></i></td>
<td><i class="fas fa-tools"></i></td>
<td><i class="fas fa-pen"></i></td>
</tr></table>
</div>
-->
*/ ?>
<div class="center login">
  <div class="row" id="login_form">
    <div class="inner_row">
      <div class="col1">
        <div>E-Mail</div>
      </div>
      <div class="col3">
        <div><input type="text" id="email" name="email<?php echo time() ?>" onkeyup="auth_may_login(event)" value="<?php echo htmlentities($email) ?>" /></div>
      </div>
    </div>
    <div class="inner_row">
      <div class="col1">
        <div>Passwort</div>
      </div>
      <div class="col3">
        <div><input type="password" id="password" onkeyup="auth_may_login(event)" /></div>
      </div>
    </div>
    <div class="inner_row mt1">
      <div class="col3">
        <div id="password_lost" class="button" onclick="location.href='/auth/password_lost?email='+$('#email').val()">Passwort vergessen</div>
      </div>
      <div class="col1 right last">
        <div id="login" class="button" onclick="auth_login()">Login</div>
      </div>
    </div>
  </div>
  <br>
  <div id="out" class="row" style="display:none;">
  </div>
</div>
