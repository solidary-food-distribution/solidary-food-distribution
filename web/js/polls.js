function polls_answer_vote(el){
  var poll_answer_id = $(el).data('id');
  var value = $(el).is(':checked')?'1':'0';
  $('#loading').show();
  $.ajax({
    type: 'POST',
    data: {
      poll_answer_id: poll_answer_id,
      value: value
    },
    url: '/polls/answer_vote_ajax',
    dataType: "json",
    success: function(data){
      $('#loading').hide();
      $('#count'+poll_answer_id).html(data.count);
    }
  });
}
