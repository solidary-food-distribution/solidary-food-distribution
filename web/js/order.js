function order_filter(el){
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
    url: '/order/filter_ajax',
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
      var newURL = '/order/?' + params.toString();
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

function order_search_keyup(event){
  if(event && event.keyCode == 13){
    $('#search_button').click();
  }
}

function order_change(el,dir){
  var product_id = $(el).closest('.product').data('id');
  var data = get_url_params();
  data['product_id'] = product_id;
  data['dir'] = dir;
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: data,
    url: '/order/change_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
    }
  });
}

function order_show_more(){
  if($('#show_more').offset().top-50 > $('footer').offset().top){
    return;
  }
  order_show_more_load();
}
function order_show_more_load(){
  $('main').off('scroll');
  $('#show_more').off('click');
  $('#show_more').html('...');
  order_filter($('#show_more'));
}