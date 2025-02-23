function inventory_filter(el){
  var data = get_url_params();
  if($(el).hasClass('search')){
    data['field'] = 'search';
    data['value'] = $('#search').val();
  }else if($(el).attr('id') == 'show_more'){
    var limit = data['limit'];
    if(limit == undefined){
      limit = 10;
    }
    limit = parseInt(limit) + 10;
    data['limit'] = limit;
    data['field'] = 'limit';
    data['value'] = limit;
  }else{
    data['field'] = $(el).closest('.options').data('field');
    data['value'] = $(el).data('value');
  }
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: data,
    url: '/inventory/filter_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      const params = new URLSearchParams(location.search);
      replace_header_main_footer(html);
      if(data['field'] != 'limit'){
        params.delete('limit');
        $('main').scrollTop(0);
      }
      params.delete(data['field']);
      params.append(data['field'], data['value']);
      var newURL = '/inventory/?' + params.toString();
      history.pushState({}, null, newURL);
      if(data['field'] == 'modus' && data['value'] == 's'){
        $('#search').select();
      }else if(data['field'] == 'search'){
        $('#search').focus();
        var val = $('#search').val();
        $('#search').val('');
        $('#search').val(val);
      }
    }
  });
}

function inventory_update(el, change){
  var product_id = $(el).closest('.product').data('id');
  var data = get_url_params();
  data['product_id'] = product_id;
  data['change'] = change;
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: data,
    url: '/inventory/update_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
    }
  });
}

function inventory_search_keyup(event){
  if(event && event.keyCode == 13){
    $('#search_button').click();
  }
}

/*
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
*/