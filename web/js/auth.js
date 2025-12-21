function auth_login(){
  $('#login').hide();
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {
      email: $('#email').val(),
      password: $('#password').val(),
    },
    url: '/auth/login_ajax?ts='+get_ts(),
    dataType: "json",
    success: function(data) {
      $('#loading').hide();
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
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {email: $('#email').val() },
    url: '/auth/password_lost_ajax?ts='+get_ts(),
    dataType: "json",
    success: function(data) {
      $('#loading').hide();
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
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {pwt: pwt, password: $('#password').val() },
    url: '/auth/password_set_ajax?ts='+get_ts(),
    dataType: "json",
    success: function(data) {
      $('#loading').hide();
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

function keyboard_ok_func(){
  var pickup_pin = $('#pickup_pin').data('pin');
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {pickup_pin: pickup_pin},
    url: '/auth/login_pin_ajax?',
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      if(json.error == ''){
        location.href = '/';
      }else{
        notify(json.error);
        $('#pickup_pin').data('pin', '');
        $('#pickup_pin').html('');
      }
    }
  });
}

function auth_shutdown(){
  $('#main').html('<div class="row">Wird heruntergefahren...</div>');
  $('#loading').show();
  $.ajax({
    type: 'GET',
    url: 'http://127.0.0.1:8008/scale?do=shutdown',
    dataType: "json",
    timeout: 3000,
    success: function(data) {
      $('#loading').hide();
      if(data.out != 'shutdown'){
        location.href='/';
      }
    },
    error: function (xhr, ajaxOptions, thrownError){
      $('#loading').hide();
      location.href='/';
    }
  });
}