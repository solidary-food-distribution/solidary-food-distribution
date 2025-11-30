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

function admin_orders_edit(el, order_item_id, pickup_item_id){
  var div = $('#admin_orders_edit').clone();
  div.attr('id', 'admin_orders_edit'+order_item_id);
  div.attr('data-order_item_id', order_item_id);
  div.attr('data-pickup_item_id', pickup_item_id);
  $(el).closest('.inner_row').after(div);
  var product_id = $(el).closest('.inner_row').data('product_id');
  div.find('select[name="product_id"]').val(product_id);
  var order_item_comment = $(el).parent().parent().find('.order_item_comment').html();
  div.find('[data-field="order_item_comment"]').val(order_item_comment);
  var amount_order = $(el).parent().parent().find('.amount_order').html();
  div.find('[data-field="amount_order"]').html(amount_order);
  var amount_pickup = $(el).parent().parent().find('.amount_pickup').html();
  div.find('[data-field="amount_pickup"]').html(amount_pickup);
  $(el).parent().parent().parent().parent().find('.button.edit').hide();
  div.css('display', 'flex');
}

function admin_orders_update(el){
  var div = $(el).closest('.inner_row');
  var order_item_id = div.data('order_item_id');
  var pickup_item_id = div.data('pickup_item_id');
  var product_id = div.find('select[name="product_id"]').val();
  var order_item_comment = div.find('[data-field="order_item_comment"]').val();
  var amount_order = div.find('[data-field="amount_order"]').html();
  var amount_pickup = div.find('[data-field="amount_pickup"]').html();
  console.log("admin_orders_update "+order_item_id+" "+product_id+" "+order_item_comment+" "+amount_order+" "+amount_pickup);
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/admin/orders_update_ajax?order_item_id='+order_item_id+'&pickup_item_id='+pickup_item_id,
    data: { 'product_id': product_id, 'order_item_comment': order_item_comment, 'amount_order': amount_order, 'amount_pickup': amount_pickup},
    dataType: 'html',
    success: function(html){
      $('#loading').hide();
      location.reload();
    }
  });
}

function admin_products_import_friedls_update(el){
  var tr = $(el).closest('tr');
  var row_id = tr.data('id');
  var field = $(el).attr('name');
  if(field == 'new'){
    field = 'new_product_name';
    el = tr.find('.new_product_name');
  }
  var value = $(el).val();
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/admin/products_import_friedls_update_ajax',
    data: { 'row_id': row_id, 'field': field, 'value': value },
    dataType: 'html',
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
    }
  });
}

function admin_poll_update(el){
  var member_id = $(el).data('member_id');
  var poll_answer_id = $(el).val();
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/admin/poll_update_ajax',
    data: { 'member_id': member_id, 'poll_answer_id': poll_answer_id },
    dataType: 'html',
    success: function(html){
      $('#loading').hide();
    }
  });
}