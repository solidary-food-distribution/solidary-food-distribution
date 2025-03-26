function pickup_filter(el){
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
    url: '/pickup/filter_ajax',
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
      var newURL = '/pickup/?' + params.toString();
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


function pickup_change(el,change){
  var product_id = $(el).closest('.product').data('id');
  var data = get_url_params();
  data['product_id'] = product_id;
  data['change'] = change;
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: data,
    url: '/pickup/change_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
    }
  });
}

/*
function pickup_change(el, value){
  var pickup_id = $(el).closest('.product').data('pickup_id');
  var item_id = $(el).closest('.product').data('item_id');
  var product_type = $(el).closest('.product').data('product_type');
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {
      pickup_id: pickup_id,
      product_type: product_type,
      item_id: item_id,
      value: value
    },
    url: '/pickup/update_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
    }
  });
}
*/


var scale_exact = 0;
var scale_min = 0;
var scale_max = 0;
var scale_price = 0;
var scale_price_sum = 0;
var scale_price_sum_pickup = 0;
var scale_pickup_id = 0;
var scale_item_id = 0;
var scale_product_type = '';
var scale_edit_mode = 0;
var scale_row = 0;
function scale_show(el){
  scale_row = $(el).closest('.row');
  $('#scale_title').html($(el).data('title'));
  if(scale_edit_mode){
    $('#scale_display').text(scale_row.find('.amount_ordered').text());
    $('#scale_ok').show();
  }else{
    $('#scale_display').text('Verbindung zur Waage...');
    $('#scale_ok').hide();
  }
  $('#scale_title2').text($(el).data('title2'));
  $('#scale_bottom').text($(el).data('bottom'));
  scale_exact = $(el).data('value_exact');
  scale_min = $(el).data('value_min');
  scale_max = $(el).data('value_max');
  scale_price = $(el).data('price');
  scale_price_sum = $(el).data('price_sum');
  scale_price_sum_pickup = $(el).data('price_sum_pickup');
  scale_pickup_id = scale_row.data('pickup_id');
  scale_item_id = scale_row.data('item_id');
  scale_product_type = scale_row.data('product_type');
  $('#scale').show();
  scale_show_values();
}

function scale_show_values(){
  var text = '0';
  if(scale_edit_mode){
    text = $('#scale_display').text().replace(',', '.').replace(' kg', '');
    $('#scale_unit').css('display', 'inline-block');
  }else{
    $('#scale_unit').hide();
  }
  var value = parseFloat(text);
  //scale_show_price(value);
  scale_show_bar(value);
  if(!scale_edit_mode){
    scale_read();
  }
}

function scale_ok(){
  if(scale_read_timeout){
    clearTimeout(scale_read_timeout);
    scale_read_timeout=0;
  }
  var value = $('#scale_display').text();
  value = value.replace(' kg', '');
  value = value.replace(',', '.');
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {
      pickup_id: scale_pickup_id,
      product_type: scale_product_type,
      item_id: scale_item_id,
      value: value,
    },
    url: '/pickup/scale_ajax',
    dataType: 'html',
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
      if(scale_edit_mode){
        scale_edit();
      }
    }
  });
}

var scale_read_timeout = 0;
function scale_read(){
  if(scale_read_timeout){
    clearTimeout(scale_read_timeout);
    scale_read_timeout=0;
  }
  $.ajax({
    type: 'GET',
    url: 'http://127.0.0.1:8008/scale?do=read',
    dataType: "json",
    timeout: 3000,
    success: function(data) {
      //console.log(data);
      var out = data.out;
      scale_show_bar(out);
      //scale_show_price(out);
      
      out = out.replace('.', ',');
      $('#scale_display').html(out);
      $('#scale_ok').show();
      if($('#scale').css('display') != 'none'){
        scale_read_timeout = setTimeout(scale_read, 250);
      }
    },
    error: function (xhr, ajaxOptions, thrownError){
      var error = '';
      if(xhr.status == 0 && xhr.statusText == 'error'){
        error = 'Verbindungsfehler?';
      }
      $('#scale_ok').hide();
      $('#scale_display').html('Waage Fehler: '+xhr.statusText+' '+error);
    }
  });
}

function scale_show_bar(value){
  if(!scale_exact){
    $('#scale_bar').hide();
    return;
  }
  $('#scale_bar').show();
  value = value.toString().replace(' kg', '');
  value = parseFloat(value);
  var color = '';
  if(value>0 && value<scale_min){
    color='yellow';
  }else if(value>0 && value<=scale_max){
    color='green';
  }else if(value>0 && value>scale_max){
    color='red';
  }
  if(!color){
    color='unset';
  }
  $('#bar').css('background-color', color);
  var width = Math.round(value / scale_max * 100);
  if(width>100){
    width=100;
  }
  $('#bar').css('width', 'calc('+width.toString()+'% - 2px)');
  $('#bar_okay').css('left' , Math.round(scale_min / scale_max * 100).toString()+'%');
  $('#bar_exact').css('left' , Math.round(scale_exact / scale_max * 100).toString()+'%');
}

function scale_tare(){
  $.ajax({
    type: 'GET',
    url: 'http://127.0.0.1:8008/scale?do=tare',
    dataType: "json",
    timeout: 3000,
    success: function(data) {
      //console.log(data);
    },
    error: function (xhr, ajaxOptions, thrownError){
      var error = '';
      if(xhr.status == 0 && xhr.statusText == 'error'){
        error = 'Verbindungsfehler?';
      }
      $('#scale_ok').hide();
      $('#scale_display').html('Waage Fehler: '+xhr.statusText+' '+error);
    }
  });
}

function scale_hide(){
  if(scale_read_timeout){
    clearTimeout(scale_read_timeout);
    scale_read_timeout=0;
  }
  $('#scale').hide();
  //$('.ctrl.weight .button.scale').show();
  //keyboard_ok_func = 0;
}

function scale_edit(){
  if(scale_read_timeout){
    clearTimeout(scale_read_timeout);
    scale_read_timeout=0;
  }
  scale_edit_mode = 1;
  $('#scale_display').text(scale_row.find('.amount_ordered').text());
  $('#scale_display').addClass('input').addClass('active');
  $('#scale_keyboard').show();
  $('#scale_edit').css('display', 'none');
  $('#scale_scale').css('display', 'inline-flex');
  scale_show_values();
  $('#scale_ok').show();
  keyboard_input_change_func = function(){
    scale_show_values();
  }
}
function scale_scale(){
  scale_edit_mode = 0;
  $('#scale_display').removeClass('input').removeClass('active');
  $('#scale_keyboard').hide();
  $('#scale_scale').css('display', 'none');
  $('#scale_edit').css('display', 'inline-flex');
  $('#scale_ok').hide();
  scale_show_values();
}