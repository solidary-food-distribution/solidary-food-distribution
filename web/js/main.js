function document_ready(){
  //var mobile=is_mobile();
}

function is_mobile(){
  let check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
};

function get_ts(){
  var date = new Date();
  return date.getTime();
}

function replace_header_main_footer(html){
  var header_pos=html.indexOf('</HEADER>');
  if(header_pos>=0){
    var header=html.substr(0,header_pos+9);
    html=html.substr(header_pos+9);
    header=header.replace('<HEADER>','').replace('</HEADER>','');
    $('#header').html(header);
  }
  var footer_pos=html.indexOf('<FOOTER>');
  if(footer_pos>=0){
    var footer=html.substr(footer_pos);
    html=html.substr(0,footer_pos);
    footer=footer.replace('<FOOTER>','').replace('</FOOTER>','');
    $('#footer').html(footer);
  }
  $('#main').html(html);
}



function ajax_id_replace(id, url){
  $.ajax({
    type: 'POST',
    url: url,
    dataType: 'html',
    success: function(html){
      $('#'+id).replaceWith(html);
    }
  });
}

function ajax_field_update(id, field, url){
  var value = $('#'+id).val();
  $.ajax({
    type: 'POST',
    data: {
      field: field,
      value: value
    },
    url: url,
    dataType: 'json',
    success: function(data){
      if(data && data.value){
        $('#'+id).val(data.value);
      }
    }
  });
}

function validate_money(id){
  var value = $('#'+id).val();
}

function input_onfocus(el){
  if($(el).hasClass('active')){
    return;
  }
  active_input_post_value();
  $('.input.active').removeClass('active');
  $(el).addClass('active');
  var value = $(el).data('value');
  keyboard_show(el);
}

var active_input_post_value_goto_url='';
function active_input_post_value_goto(url){
  active_input_post_value_goto_url = url;
  active_input_post_value();
}


function active_input_post_value(){
  var input = $('.input.active');
  if(!input.length){
    return;
  }
  console.log("active_input_post_value "+input.data('field'));
  var type = input.data('type');
  var value = '';
  if(type == 'options'){
    value = input.find('input[type="radio"]').val();
  }else{
    value = input.text().trim();
  }
  if(input.data('regexp')){
    var regexp = new RegExp(input.data('regexp'));
    if( !regexp.test(value) ){
      alert(input.data('regexp_fail'));
      return;
    }
  }
  $.ajax({
    type: 'POST',
    data: {
      field: input.data('field'),
      type: type,
      value: value
    },
    url: input.data('url'),
    dataType: 'json',
    success: function(data){
      if(data.location_href){
        location.href = data.location_href;
        return;
      }
      highlight_input(input);
      if(active_input_post_value_goto_url.length){
        location.href = active_input_post_value_goto_url;
      }
    }
  });
}



var highlight_inputs=[];
function highlight_input(el){
  highlight_inputs.push({el:el, cycles: 20});
  highlight_input_timer();
}
var highlight_inputs_timer=0;
function highlight_input_timer(){
  if(highlight_inputs_timer){
    clearTimeout(highlight_inputs_timer);
  }
  for(var i = 0; i < highlight_inputs.length; i++){
    highlight_inputs[i].cycles--;
    var rval = 255 - highlight_inputs[i].cycles*10;
    var gval = 255 - highlight_inputs[i].cycles*2;
    var bval = 255 - highlight_inputs[i].cycles*10;
    var value = 'rgb('+rval+','+gval+','+bval+')';
    //console.log(value);
    highlight_inputs[i].el.css('background-color', value);
    if(highlight_inputs[i].cycles <= 0){
      highlight_inputs.shift();
      i--;
    }
  }
  highlight_inputs_timer = setTimeout(highlight_input_timer,15);
}



function keyboard_show(el){
  var type = $(el).data('type');
  $('#keyboard_window').removeClass().addClass(type);
  $('#keyboard_info').html($(el).data('info'));
  $('#key_shift').hide();
  $('.keyboard_sub').hide();
  if(type == 'weight' || type == 'pieces' || type == 'money'){
    $('#keyboard_numbers').show();
  }else if(type == 'options'){
    $('#keyboard_options').html('');
    $(el).find('.option').each(function(){
      var input = $(this).find('input[type="radio"]');
      var label = $(this).find('label');
      var option = $('<div><div onclick="keyboard_option(\''+$(input).attr('id')+'\')" class="'+($(input).is(':checked')?'checked':'')+'"><span>'+$(label).html()+'</span></div></div>');
      $('#keyboard_options').append(option);
    });
    $('#keyboard_options').show();
  }else if(type == 'string'){
     $('#keyboard_string_upper').show();
     $('#key_shift').show();
     $('#key_shift').addClass('active');
  }
  var inputs = $('.input[data-field]');
  if(inputs.length<=1){
    $('#keyboard_header .button').hide();
  }

  $('body').off('keydown');
  $('body').keydown(keyboard_keydown);
  $('#keyboard').show();
}

function keyboard_keydown(event){
  var key = event.originalEvent.key;
  var shiftKey = event.originalEvent.shiftKey;
  if(shiftKey && key.length>1){
    key = 'Shift '+key;
  }
  if( keyboard_key(key) ){
    event.preventDefault();
  }
}

function keyboard_key(key){
  //console.log("keyboard_key "+key);
  if('|0|1|2|3|4|5|6|7|8|9|,|Backspace|'.indexOf('|'+key+'|') >= 0 ){
    keyboard_input_change(key);
  }else if('|A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|Ä|Ö|Ü|'.indexOf('|'+key+'|') >= 0 ){
    keyboard_input_change(key);
  }else if('|a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|ä|ö|ü|ß|'.indexOf('|'+key+'|') >= 0 ){
    keyboard_input_change(key);
  }else if('| |-|'.indexOf('|'+key+'|') >= 0 ){
    keyboard_input_change(key);
  }else if(key == 'Ok'){
    keyboard_ok();
  }else if(key == 'Shift Tab'){
    keyboard_prev_input();
  }else if(key == 'Tab'){
    keyboard_next_input();
  }else if(key.substr(-5) == 'Shift'){
    keyboard_toggle_shift();
  }else if(key.substr(-6) == 'Escape'){
    $('body').off('keydown');
    $('#keyboard').hide();
    $('#keyboard').html('');
  }else if(key.substr(-2) == 'F5'){
    return false;
  }else{
    console.log('keyboard_key '+key);
  }
  return true;
}

function keyboard_ok(){
  $('.button.ok').click();
}

function keyboard_option(radio_id){
  $('#'+radio_id).click();
  keyboard_next_input();
}

function keyboard_input_change(key){
  //console.log("keyboard_input_change "+key);
  var value = $('.input.active').html().trim();
  if(value == '&nbsp;'){
    value = '';
  }else{
    value = value.replace('&nbsp;', ' ');
  }
  var type = $('.input.active').data('type');
  if(key == 'Backspace'){
    if(type == 'options'){
      $('.input.active input:checked').prop('checked', false);
      $('#keyboard_options .checked').removeClass('checked');
      return;
    }
    if(value.length >= 1){
      value = value.substr(0,value.length-1);
    }
  }else if(key == ',' && value.indexOf(',') >= 0){
    //nothing
  }else if(type == 'weight' && value.lastIndexOf(',') >= 0 && value.lastIndexOf(',') <= value.length-4){
    //nothing
  }else if(type == 'weight' && value.length >= 7){
    //nothing
  }else if(type == 'pieces' && value.lastIndexOf(',') >= 0 && value.lastIndexOf(',') <= value.length-3){
    //nothing
  }else if(type == 'pieces' && value.length >= 6){
    //nothing
  }else if(type == 'money' && value.lastIndexOf(',') >= 0 && value.lastIndexOf(',') <= value.length-3){
    //nothing
  }else if(type == 'money' && value.length >= 6){
    //nothing
  }else{
    value += key;
  }
  if($('#key_shift').hasClass('active')){
    //after upper letter deactivate Shift
    if(value.length >= 1  && value.substr(-2,1).toUpperCase() == value.substr(-2,1)){
      keyboard_toggle_shift();
    }
  }else{
    if(value.length == 0 || (value.length >= 1  && value.substr(-1) == ' ')){
      //at start or after space activate Shift
      keyboard_toggle_shift();
    }
  }

  if(value.trim() == ''){
    value = ' ';
  }
  $('.input.active').html(value.replace(' ','&nbsp;'));
}

function keyboard_toggle_shift(){
  if($('#key_shift').is(':hidden')){
    return;
  }
  if($('#key_shift').hasClass('active')){
    $('#keyboard_string_upper').hide();
    $('#keyboard_string_lower').show();
    $('#key_shift').removeClass('active');
  }else{
    $('#keyboard_string_lower').hide();
    $('#keyboard_string_upper').show();
    $('#key_shift').addClass('active');
  }
}

function keyboard_prev_input(){
  var active = $('.input.active');
  var inputs = $('.input[data-field]');
  var index = -1;
  for(var i = 0; i < inputs.length; i++){
    if($(inputs[i]).data('field') == active.data('field')){
      index = i;
      break;
    }
  }
  if(index<0){
    return;
  }
  index--;
  if(index<0){
    index = inputs.length - 1;
  }
  inputs[index].click();
}

function keyboard_next_input(){
  var active = $('.input.active');
  var inputs = $('.input[data-field]');
  var index = -1;
  for(var i = 0; i < inputs.length; i++){
    if($(inputs[i]).data('field') == active.data('field')){
      index = i;
      break;
    }
  }
  if(index<0){
    return;
  }
  if(index+1 >= inputs.length){
    index = 0;
  }else{
    index++;
  }
  inputs[index].click();
}




