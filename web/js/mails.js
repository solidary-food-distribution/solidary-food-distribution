function mails_send(mail_id){
  if(!confirm('Wirklich E-Mail senden?')){
    return;
  }
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {mail_id: mail_id},
    url: '/mails/send_ajax',
    dataType: 'json',
    success: function(json){
      if(json.error){
        alert(json.error);
        $('#loading').hide();
      }else{
        location.href='/mails';
      }
    }
  });
}

function mails_to_all_users(el){
  var input_to = $('.input[data-field="to"]');
  if($(el).is(':checked')){
    input_to.val('[ALL_USERS]');
  }else{
    input_to.val('');
  }
  input_onfocus(input_to);
  input_text_onchange(input_to);
}