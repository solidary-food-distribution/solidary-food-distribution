function delivery_item_delete(delivery_id, item_id){
  $.ajax({
    type: 'POST',
    data: {delivery_id: delivery_id, item_id: item_id},
    url: '/delivery/item_delete_ajax',
    dataType: 'html',
    success: function(html){
      location.href='/delivery?id='+delivery_id;
    }
  });
}
