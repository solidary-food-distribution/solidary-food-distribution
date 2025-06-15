function admin_products_filter(el){
  var data = get_url_params();
  console.log(data);
  if(data.product_id != undefined){
    delete data.product_id;
  }
  console.log(data);
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

function admin_products_update_ajax(el){
  var field = $(el).data('field');
  var value = $(el).val();
  var url = $(el).data('url');
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {'field': field, 'value': value},
    url: url,
    dataType: "json",
    success: function(html){
      $('#loading').hide();
    }
  });
}

function admin_purchase_date(purchase_id, field, value){
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {'field': field, 'value': value},
    url: '/admin/purchase_date_ajax?purchase_id='+purchase_id,
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      if($('#purchase_date_ajax').length){
        $('#purchase_date_ajax').replaceWith(html);
      }else{
        $('#main').append(html);
      }
    }
  });
}

function admin_purchase_status(purchase_id){
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/admin/purchase_status_ajax?purchase_id='+purchase_id,
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      location.reload();
    }
  });
}