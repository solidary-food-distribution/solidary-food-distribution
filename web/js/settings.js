function privacy_update(el){
  var setting = $(el).attr('name');
  var value = $(el).attr('value');
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {setting: setting, value: value},
    url: '/settings/privacy_update_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
    }
  });
}