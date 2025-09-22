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

function mails_add_answer(answer_id){
  var textarea = $('#mail_content');
  var start = textarea[0].selectionStart;
  console.log(start);
  var content = textarea.val();
  console.log(content);
  var label = 'Empfang bestätigt';
  if(answer_id>1){
    label = 'Antwort '+answer_id;
  }
  content = content.substr(0, start) + '[button value="'+answer_id+'" label="'+label+'"]' + content.substr(start);
  console.log(content);
  textarea.val(content);
  input_onfocus(textarea);
  input_text_onchange(textarea);
}