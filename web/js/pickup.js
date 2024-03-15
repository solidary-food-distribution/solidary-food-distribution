function filter_options(el){
  var value = $(el).data('value');
  var field = $(el).data('field');
  if($(el).hasClass('option')){
    field = $(el).closest('.options').data('field');
  }
  var pickup_id = $('.row.product').first().data('pickup_id');
  location.href = '/pickup?pickup_id='+pickup_id+'&'+field+'='+value;
}


function pickup_change(el, value){
  var pickup_id = $(el).closest('.product').data('pickup_id');
  var item_id = $(el).closest('.product').data('item_id');
  var product_type = $(el).closest('.product').data('product_type');
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
      replace_header_main_footer(html);
    }
  });
}


var scale_exact = 0;
var scale_min = 0;
var scale_max = 0;
var scale_price = 0;
var scale_price_sum = 0;
var scale_price_sum_pickup = 0;
var scale_pickup_id = 0;
var scale_item_id = 0;
var scale_product_type = '';
function scale_show(el){
  var row = $(el).closest('.row');
  $('#scale_title').text($(el).data('title'));
  $('#scale_display').text('Verbindung zur Waage...');
  $('#scale_title2').text($(el).data('title2'));
  scale_exact = $(el).data('value_exact');
  scale_min = $(el).data('value_min');
  scale_max = $(el).data('value_max');
  scale_price = $(el).data('price');
  scale_price_sum = $(el).data('price_sum');
  scale_price_sum_pickup = $(el).data('price_sum_pickup');
  scale_pickup_id = row.data('pickup_id');
  scale_item_id = row.data('item_id');
  scale_product_type = row.data('product_type');
  $('#scale_ok').hide();
  scale_show_price(0);
  scale_show_bar(0);
  $('#scale').show();
  scale_read();
}

function scale_ok(){
  if(scale_read_timeout){
    clearTimeout(scale_read_timeout);
    scale_read_timeout=0;
  }
  var value = $('#scale_display').text();
  value = value.replace(' kg', '');
  value = value.replace(',', '.');
  $.ajax({
    type: 'POST',
    data: {
      pickup_id: scale_pickup_id,
      product_type: scale_product_type,
      item_id: scale_item_id,
      value: value,
    },
    url: '/pickup/update_ajax',
    dataType: 'html',
    success: function(html){
      replace_header_main_footer(html);
    }
  });
  //filter_options($('.filters .filter[data-field="product_type"] .option.selected'));
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
      scale_show_price(out);
      
      out = out.replace('.', ',');
      $('#scale_display').html(out);
      $('#scale_ok').show();
      scale_read_timeout = setTimeout(scale_read, 200);
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

function scale_show_price(value){
  if(!scale_price){
    $('#scale_price').hide();
    $('#scale_price_sum').hide();
    return;
  }
  $('#scale_price').show();
  value = value.toString().replace(' kg', '');
  value = parseFloat(value);
  var sum = value * scale_price;
  var text = ' x ' + format_money(scale_price) + ' EUR = ' + format_money(sum) + ' EUR';
  $('#scale_price').text(text);
  if(scale_price_sum){
    text = 'Gemüseanteil ' + format_money(scale_price_sum_pickup + sum) + ' EUR von ' + format_money(scale_price_sum) + ' EUR';
    $('#scale_price_sum').text(text);
    $('#scale_price_sum').show();
    scale_show_bar(scale_price_sum_pickup + sum);
  }else{
    $('#scale_price_sum').text('');
    $('#scale_price_sum').hide();
    scale_show_bar(value);
  }
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

function scale_hide(){
  if(scale_read_timeout){
    clearTimeout(scale_read_timeout);
    scale_read_timeout=0;
  }
  $('#scale').hide();
  //$('.ctrl.weight .button.scale').show();
  //keyboard_ok_func = 0;
}
