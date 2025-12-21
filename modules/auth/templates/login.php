<div class="center login">
  <?php if(isset($_SESSION['scale']) && $_SESSION['scale']): ?>
    <div class="row" id="pin_form">
      <div class="inner_row">
        <div class="col6">
          <div id="pickup_pin" class="input active" style="width:100%;font-size: 3em;" data-pin=""></div>
        </div>
      </div>
    </div>
    <?php
      require('keyboard.part.php'); 
    ?>
    <script type="text/javascript">
      $('#keyboard_window > div').hide();
      $('#keyboard_key_comma').addClass('none');
      $('#key_shift').hide();
      $('#keyboard_numbers').show();
      $('#keyboard_ctrl').show();
      $('#keyboard').show();
      keyboard_input_change_func = function(){
        var pin_input = $('#pickup_pin').text().trim();
        if(pin_input.length > 6){
          pin_input = pin_input.substr(0, 6);
          $('#pickup_pin').html(pin_input);
          return;
        }
        var pin = $('#pickup_pin').data('pin');
        if(pin_input.length && pin_input.substr(-1, 1) != 'X'){
          pin = pin.substr(0, pin_input.length - 1);
          pin += pin_input.substr(-1, 1);
        }
        pin = pin.substr(0, pin_input.length);
        $('#pickup_pin').data('pin', pin);
        if(pin_input.length){
          pin_input = 'X'.repeat(pin_input.length - 1) + pin_input.substr(-1, 1);
          $('#pickup_pin').html(pin_input);
        }
      }
    </script>
    <br>
    <div class="button large" style="width:auto;position:absolute;bottom:-1em;left:0em;height:auto;line-height:1em;" onclick="auth_shutdown()">Herunterfahren<br><span style="font-size:50%">(<?php echo $others?$others.' offene Abholungen':'Keine offenen Abholungen' ?>)</span></div>
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