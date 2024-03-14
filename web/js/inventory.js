function inventory_remove_product(product_id){
  active_input_post_value();
  if(!confirm('Wirklich dieses Produkt auf Menge 0 setzen?')){
    return;
  }
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