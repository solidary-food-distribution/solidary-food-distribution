function admin_products_filter(el){
  var data = get_url_params();
  if($(el).hasClass('search')){
    //data['field'] = 'search';
    //data['value'] = $('#search').val();
  }else{
    data['field'] = $(el).closest('.options').data('field');
    data['value'] = $(el).data('value');
  }
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: data,
    url: '/admin/products_filter_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      const params = new URLSearchParams(location.search);
      replace_header_main_footer(html);
      params.delete(data['field']);
      params.append(data['field'], data['value']);
      var newURL = '/admin/products?' + params.toString();
      history.pushState({}, null, newURL);
      if(data['field'] == 'modus' && data['value'] == 's'){
        //$('#search').select();
      }else if(data['field'] == 'search'){
        //$('#search').focus();
        //var val = $('#search').val();
        //$('#search').val('');
        //$('#search').val(val);
      }
    }
  });
}