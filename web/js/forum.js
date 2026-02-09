function template_replace(data){
  for (const [key, value] of Object.entries(data)) {
    document.body.innerHTML = document.body.innerHTML.replace(new RegExp('{{'+key+'}}', 'g'), value);
  }
}

function template_show_list(template, data){
  for (const element of data) {
    //console.log(element);
    var tmpl = $('#'+template).clone();
    tmpl.attr('id',template+'-'+element.id);
    for (var [key, value] of Object.entries(element)) {
      if(value === null){
        value = '';
      }
      tmpl.html(tmpl.html().replace('{{'+key+'}}', value.replaceAll('\n','<br>')));
    }
    $('#'+template).before(tmpl);
    tmpl.show();
  }
}

function forum_restore_input(context, context_id){
  const param = 'forum-' + context + '-' + context_id;
  var value = localStorage.getItem(param);
  if(!value){
    value = '{}';
  }
  const object = JSON.parse(value);
  for (var [key, value] of Object.entries(object)) {
    $('#' + key).val(value);
  }
}

function forum_save_input(el, context, context_id){
  const input_name = $(el).attr('id');
  const param = 'forum-' + context + '-' + context_id;
  var value = localStorage.getItem(param);
  if(!value){
    value = '{}';
  }
  var object = JSON.parse(value);
  object[input_name] =  $(el).val();
  localStorage.setItem(param, JSON.stringify(object));
}

function forum_ready(){
  $('.pathbar').css('display', 'inline-block');
  $('.ready_display_block').css('display', 'block');
}

function forum_init(){
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/forum/index_ajax',
    data: {},
    dataType: 'json',
    success: function(json){
      console.log(json);
      $('#loading').hide();
      template_show_list('TMPL_FORUM_ROW', json.forums);
      forum_ready();
    }
  });
}

function forum_forum_init(){
  const params = new URLSearchParams(window.location.search);
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/forum/forum_ajax?id='+params.get('id'),
    data: {},
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      //console.log(json);
      template_replace(json.forum);
      template_show_list('TMPL_TOPIC_ROW', json.topics);
      forum_ready();
    }
  });
}

function forum_topic_new_init(){
  const params = new URLSearchParams(window.location.search);
  const forum_id = params.get('forum_id');
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/forum/topic_new_ajax?forum_id='+forum_id,
    data: {},
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      //console.log(json);
      template_replace(json.forum);
      forum_restore_input('topic_new', forum_id);
      forum_ready();
    }
  });
}

var forum_topic_new_post_running = false;
function forum_topic_new_post(){
  if(forum_topic_new_post_running){
    return;
  }
  forum_topic_new_post_running = true;
  const params = new URLSearchParams(window.location.search);
  const forum_id = params.get('forum_id');
  var data = {
    'topic_name': $('#topic_name').val(),
    'post_text': $('#post_text').val()
  };
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/forum/topic_new_post_ajax?forum_id='+forum_id,
    data: data,
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      console.log(json);
      if(json.error != ''){
        notify(json.error);
      }else{
        localStorage.removeItem('forum-topic_new-'+forum_id);
        location.href='/forum/topic?id='+json.topic_id+'#post'+json.post_id;
      }
      forum_topic_new_post_running = false;
    }
  });
}

function forum_topic_init(){
  const params = new URLSearchParams(window.location.search);
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/forum/topic_ajax?id='+params.get('id'),
    data: {},
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      //console.log(json);
      template_replace(json.topic);
      template_show_list('TMPL_POST_ROW', json.posts);
      forum_ready();
    }
  });
}

function forum_post_new_init(){
  const params = new URLSearchParams(window.location.search);
  const topic_id = params.get('topic_id');
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/forum/post_new_ajax?topic_id='+params.get('topic_id'),
    data: {},
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      //console.log(json);
      template_replace(json.topic);
      forum_restore_input('post_new', topic_id);
      forum_ready();
    }
  });
}

var forum_post_new_post_running = false;
function forum_post_new_post(){
  if(forum_post_new_post_running){
    return;
  }
  forum_post_new_post_running = true;
  const params = new URLSearchParams(window.location.search);
  const topic_id = params.get('topic_id');
  var data = {
    'post_text': $('#post_text').val()
  };
  $('#loading').show();
  $.ajax({
    type: 'POST',
    url: '/forum/post_new_post_ajax?topic_id='+topic_id,
    data: data,
    dataType: 'json',
    success: function(json){
      $('#loading').hide();
      console.log(json);
      if(json.error != ''){
        notify(json.error);
      }else{
        localStorage.removeItem('forum-post_new-'+json.topic_id);
        location.href='/forum/topic?id='+json.topic_id+'#post'+json.post_id;
      }
      forum_post_new_post_running = false;
    }
  });
}

