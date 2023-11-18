<div class="center login">
  <?php if(empty($message)): ?>
    <div class="row" id="reset_form">
      <div class="inner_row">
        <div class="col2">
          <div>Neues Passwort</div>
        </div>
        <div class="col3">
          <div><input type="password" id="password" /></div>
        </div>
      </div>
      <div class="inner_row mt1">
        <div class="col2 right last">
          <div id="password_set" class="button" onclick="password_set('<?php echo $pwt ?>')">Speichern</div>
        </div>
      </div>
    </div>
  <?php endif ?>
  <br>
  <div id="out" class="row" style="<?php echo $message?'':'display:none;' ?>"><?php echo $message ?></div>
</div>
