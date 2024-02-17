<?php

require_once('pin.include.php');

$icons = PIN_ICONS;
shuffle($icons); //0-31, no key preserve
$pin_ids = array_flip(PIN_ICONS);

?>
<div id="pin">
  <div id="pin_window">
    <div id="pin_header">
      <div id="pin_title"><?php echo $pin_title ?></div>
    </div>
    <?php for($rowi = 0; $rowi < 4; $rowi++): ?>
      <div class="keyboard_keys icons">
        <div>
        <?php for($keyi = 0; $keyi < 8; $keyi++): ?>
          <?php $icon = $icons[$rowi*8 + $keyi]; ?>
          <div onclick="pin_click(this)" data-id="<?php echo $pin_ids[$icon] ?>"><i class="fas fa-<?php echo $icon ?>"></i></div>
        <?php endfor ?>
        </div>
      </div>
    <?php endfor ?>
    <div class="keyboard_keys">
      <div>
        <div onclick="pin_cancel()"><span><i class="fa-solid fa-xmark"></i></span></div>
        <div id="pin_backspace_button" onclick="pin_backspace()"><span><i class="fa-solid fa-delete-left"></i></span></div>
        <div id="pin_ok_button" onclick="pin_ok()"><span><i class="fa-solid fa-check"></i></span></div>
      </div>
    </div>
  </div>
</div>