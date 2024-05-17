function delivery_delete(delivery_id){
  if(!confirm('Wirklich Lieferung löschen?')){
    return;
  }
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {delivery_id: delivery_id},
    url: '/delivery/delete_ajax',
    dataType: 'html',
    success: function(html){
      location.href='/deliveries';
    }
  });
}

function delivery_item_delete(delivery_id, item_id){
  if(!confirm('Wirklich Position löschen?')){
    return;
  }
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {delivery_id: delivery_id, item_id: item_id},
    url: '/delivery/item_delete_ajax',
    dataType: 'html',
    success: function(html){
      location.href='/delivery?delivery_id='+delivery_id;
    }
  });
}
