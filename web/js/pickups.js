function pickups_delete(pickup_id){
  if(!confirm('Wirklich Abholung löschen?')){
    return;
  }
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {pickup_id: pickup_id},
    url: '/pickups/delete_ajax',
    dataType: 'html',
    success: function(html){
      location.href='/pickups';
    }
  });
}