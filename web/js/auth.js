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