function auth_login(){
  $('#login').hide();
  $.ajax({
    type: 'POST',
    data: {
      email: $('#email').val(),
      password: $('#password').val(),
    },
    url: '/auth/login_ajax?ts='+get_ts(),
    dataType: "json",
    success: function(data) {
      if(data.error && data.error!=''){
        $('#out').html(data.error);
        $('#out').show();
      }else{
        var url = '/';
        if($('#_forward').val().trim().length){
          url = $('#_forward').val();
        }
        if($('#_query').val().trim().length){
          url += '?'+$('#_query').val();
        }
        location.href=url;
      }
      $('#login').show();
    }
  });
}

function auth_may_login(e){
  $('#out').hide();
  if(e.key=='Enter' && $('#email').val().length && $('#password').val().length){
    auth_login();
  }
}

function auth_password_lost(){
  $('#password_lost').hide();
  $('#password').val('');
  $.ajax({
    type: 'POST',
    data: {email: $('#email').val() },
    url: '/auth/password_lost_ajax?ts='+get_ts(),
    dataType: "json",
    success: function(data) {
      if(data.message && data.message!=''){
        $('#out').html(data.message);
        $('#out').show();
        if(data.hide_form){
          $('#login_form').hide();
        }
      }
      $('#password_lost').show();
    }
  });
}

function password_set(pwt){
  $('#password_set').hide();
  $('#out').hide();
  $.ajax({
    type: 'POST',
    data: {pwt: pwt, password: $('#password').val() },
    url: '/auth/password_set_ajax?ts='+get_ts(),
    dataType: "json",
    success: function(data) {
      if(data.message && data.message!=''){
        $('#out').html(data.message);
        $('#out').show();
        if(data.hide_form){
          $('#reset_form').hide();
        }
      }
      $('#password_set').show();
    }
  });
}

var user_pickup_pin = [];
function pin_click(el){
  if(user_pickup_pin.length==6){
    return;
  }
  if($(el).hasClass('disabled')){
    return;
  }
  var id = $(el).data('id');
  user_pickup_pin.push(id);
  user_show_pickup_pin();
}

function user_show_pickup_pin(){
  var index = 0;
  $('#pin .keyboard_keys.icons div').removeClass('disabled');
  $('#pickup_pin > div').each(function(){
    if(user_pickup_pin.length-1 >= index){
      var icon_id = user_pickup_pin[index];
      var icon_name = PIN_ICONS[icon_id-1];
      if(user_pickup_pin.length-1 > index){
        icon_name = 'asterisk disabled';
      }
      $(this).html('<i class="fas fa-'+icon_name+'"></i>');
    }else{
      $(this).html('');
    }
    index++;
  });
  if(user_pickup_pin.length>=3){
    $('#pin_ok_button').removeClass('disabled');
  }else{
    $('#pin_ok_button').addClass('disabled');
  }
  if(user_pickup_pin.length>0){
    $('#pin_backspace_button').removeClass('disabled');
    $('#pin_cancel_button').removeClass('disabled');
  }else{
    $('#pin_cancel_button').addClass('disabled');
    $('#pin_backspace_button').addClass('disabled');
  }
}

function pin_backspace(){
  if($('#pin_backspace_button').hasClass('disabled')){
    return;
  }
  var id = user_pickup_pin.pop();
  user_show_pickup_pin();
}

function pin_cancel(){
  user_pickup_pin = [];
  user_show_pickup_pin();
}

function pin_ok(){
  $.ajax({
    type: 'POST',
    data: {pickup_pin: user_pickup_pin.join(',')},
    url: '/auth/login_pin_ajax',
    dataType: 'json',
    success: function(json){
      if(json.error == ''){
        user_pickup_pin = [];
        location.href = '/';
      }else{
        alert(json.error);
      }
    }
  });
}


function auth_shutdown(){
  $('#main').html('<div class="row">Wird heruntergefahren...</div>');
  $.ajax({
    type: 'GET',
    url: 'http://127.0.0.1:8008/scale?do=shutdown',
    dataType: "json",
    timeout: 3000,
    success: function(data) {
      console.log(data);
      if(data.out != 'shutdown'){
        location.href='/';
      }
    },
    error: function (xhr, ajaxOptions, thrownError){
      location.href='/';
    }
  });
}