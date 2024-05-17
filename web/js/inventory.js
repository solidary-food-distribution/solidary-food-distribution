function inventory_remove_product(product_id){
  active_input_post_value();
  if(!confirm('Wirklich dieses Produkt auf Menge 0 setzen?')){
    return;
  }
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {product_id: product_id},
    url: '/inventory/remove_product_ajax',
    dataType: 'html',
    success: function(html){
      location.href='/inventory';
    }
  });
}

function filter_options(el){
  var value = $(el).data('value');
  var field = $(el).data('field');
  if($(el).hasClass('option')){
    field = $(el).closest('.options').data('field');
    $(el).closest('.options').find('.selected').removeClass('selected');
    $(el).addClass('selected');
  }
  $('#main').html('');
  location.href = '/inventory?'+field+'='+value;
}