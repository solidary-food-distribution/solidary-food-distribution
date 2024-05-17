function pin_click(el){
  if(user_pickup_pin.length==6){
    return;
  }
  if($(el).hasClass('disabled')){
    return;
  }
  var id = $(el).data('id');
  user_pickup_pin.push(id);
  user_show_pickup_pin();
}

function user_show_pickup_pin(){
  var index = 0;
  $('#pin .keyboard_keys.icons div').removeClass('disabled');
  $('#pickup_pin > div').each(function(){
    if(user_pickup_pin.length-1 >= index){
      var icon_id = user_pickup_pin[index];
      var icon_name = PIN_ICONS[icon_id-1];
      $(this).html('<i class="fas fa-'+icon_name+'"></i>');
      $('#pin .keyboard_keys.icons div[data-id='+icon_id+']').addClass('disabled');
    }else{
      $(this).html('');
    }
    index++;
  });
  if(user_pickup_pin.length>=3){
    $('#pin_ok_button').removeClass('disabled');
  }else{
    $('#pin_ok_button').addClass('disabled');
  }
  if(user_pickup_pin.length>0){
    $('#pin_backspace_button').removeClass('disabled');
  }else{
    $('#pin_backspace_button').addClass('disabled');
  }
}

function pin_backspace(){
  if($('#pin_backspace_button').hasClass('disabled')){
    return;
  }
  var id = user_pickup_pin.pop();
  user_show_pickup_pin();
}

function pin_cancel(){
  location.href = '/user';
}

function pin_ok(){
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {pickup_pin: user_pickup_pin.join(',')},
    url: '/user/pickup_pin_ajax',
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      if(json.error == ''){
        location.href = '/user';
      }else{
        alert(json.error);
      }
    }
  });
}