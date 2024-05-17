function preferences_select(product_id, value){
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {product_id: product_id, value: value},
    url: '/preferences/select_ajax',
    dataType: 'html',
    success: function(html){
      $('#loading').hide();
      var value = html;
      var el = $('#product'+product_id+' .preferences .value'+value);
      $('#product'+product_id+' .preferences .checked').removeClass('checked');
      el.addClass('checked');
      highlight_input(el);
    }
  });
}
