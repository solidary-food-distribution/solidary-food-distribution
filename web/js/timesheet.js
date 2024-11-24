

function timesheet_edit(el,id){
  $(el).off("click");
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {id: id},
    url: '/timesheet/edit_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
    }
  });
}

function timesheet_save(el,id){
  $(el).off("click");
  var date=$('#input_date').data('value');
  var mins=$('#input_mins').val();
  var km=$('#input_km').val();
  var topic=$('#input_topic').val();
  var what=$('#input_what').val();
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {id: id, date: date, mins: mins, km: km, topic: topic, what: what},
    url: '/timesheet/save_ajax',
    dataType: "html",
    success: function(html){
      $('#loading').hide();
      replace_header_main_footer(html);
    }
  });
}

function timesheet_date_change(days){
  var ms = Date.parse($('#input_date').data('value'));
  ms += days*24*60*60*1000;
  if(ms>Date.parse(get_current_date())){
    return;
  }
  date=new Date(ms);
  var options = { weekday: 'short', year: 'numeric', month: 'numeric', day: 'numeric' };
  $('#input_date').html(date.toLocaleDateString("de-DE", options));
  $('#input_date').data('value',date.toISOString().substring(0,10));
}

function get_current_date(){
  var date = new Date();
  const offset = date.getTimezoneOffset();
  date = new Date(date.getTime() - (offset*60*1000));
  return date.toISOString().split('T')[0];
}
