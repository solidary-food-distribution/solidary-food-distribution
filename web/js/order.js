

function order_change(el,dir){
  var product_id=$(el).closest('.product').data('product_id');
  var amount=$(el).closest('.ctrl').data('amount');
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {product_id: product_id, dir: dir, amount: amount},
    url: '/order/change_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
    }
  });
}


