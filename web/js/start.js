function start_forum_read(){
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/start/forum_read_ajax',
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      $('#forum_posts').hide(500);
    }
  });
}


/*
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
*/