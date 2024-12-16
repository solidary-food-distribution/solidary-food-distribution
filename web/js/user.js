function user_show_pin(el){
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/user/show_pin_ajax',
    dataType: 'html',
    success: function(html){
      $('#loading').hide();
      $(el).parent().html(html)
    }
  });
}
