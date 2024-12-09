<div id="scale" style="display:none">
  <div id="scale_window">
    <div id="scale_header">
      <!--<div class="button" onclick="scale_prev_input()"><span><i class="fa-solid fa-caret-left"></i></span></div>-->
      <div id="scale_title"></div>
      <!--<div class="button" onclick="scale_next_input()"><span><i class="fa-solid fa-caret-right"></i></span></div>-->
    </div>
    <div id="scale_info">
      <div id="scale_display" data-type="weight"></div><div id="scale_unit"> kg</div><br>
      <!--
      <div id="scale_price"></div>
      <div id="scale_price_sum"></div>
      -->
    </div>
    <div id="scale_bar">
      <div id="bar_border"></div>
      <div id="bar_okay"></div>
      <div id="bar"></div>
      <div id="bar_exact"></div>
    </div>
    <div id="scale_keyboard" class="keyboard_keys keyboard_sub">
      <div>
        <div onclick="keyboard_key('1')"><span>1</span></div>
        <div onclick="keyboard_key('2')"><span>2</span></div>
        <div onclick="keyboard_key('3')"><span>3</span></div>
      </div>
      <div>
        <div onclick="keyboard_key('4')"><span>4</span></div>
        <div onclick="keyboard_key('5')"><span>5</span></div>
        <div onclick="keyboard_key('6')"><span>6</span></div>
      </div>
      <div>
        <div onclick="keyboard_key('7')"><span>7</span></div>
        <div onclick="keyboard_key('8')"><span>8</span></div>
        <div onclick="keyboard_key('9')"><span>9</span></div>
      </div>
      <div>
        <div onclick="keyboard_key(',')"><span>,</span></div>
        <div onclick="keyboard_key('0')"><span>0</span></div>
        <div onclick="keyboard_key('Backspace')"><span><i class="fa-solid fa-delete-left"></i></span></div>
      </div>
    </div>
    <div id="scale_ctrl" class="keyboard_keys">
      <div>
        <div id="scale_edit" onclick="scale_edit()"><span><i class="fa-solid fa-pencil"></i></span></div>
        <div id="scale_scale" onclick="scale_scale()"><span><i class="fa-solid fa-weight-scale"></i></span></div>
        <div onclick="scale_hide()"><span><i class="fa-solid fa-xmark"></i></span></div>
        <div onclick="scale_ok()" id="scale_ok"><span><i class="fa-solid fa-check"></i></span></div>
      </div>
    </div>
  </div>
</div>