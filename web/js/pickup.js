function filter_options(el){
  var div = $(el).closest('.input');
  div.find('.option.selected').removeClass('selected');
  $(el).addClass('selected');
  var field = div.data('field');
  var value = $(el).data('value');
  $('.row.product').each(function(){
    if($(this).data(field) == value){
      $(this).show();
    }else{
      $(this).hide();
    }
  });
}