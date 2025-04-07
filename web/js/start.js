function start_info_read(info_id){
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {info_id: info_id},
    url: '/start/info_read_ajax',
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      $('#info'+info_id).hide(500);
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