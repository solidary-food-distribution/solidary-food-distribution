<?php
$PROPERTIES['pathbar']=array(
  '/forum'=>'Forum',
  '/forum/forum?id={{forum_id}}'=>'{{forum_name}}',
  ''=>'{{topic_name}}',
);
?>

<div class="row ready_display" data-display="flex" style="display:none">
  <div class="col13">
    <b>{{topic_name}}</b>
  </div>
  <div class="col1 last right">
    <div class="button show{{editable}}" onclick="location.href='/forum/topic_edit?id={{topic_id}}';">
      <i class="fa-solid fa-pencil"></i>
    </div>
  </div>
</div>

<div id="TMPL_POST_ROW" class="row" style="display:none">
  <a name="post{{id}}"></a>
  <div class="inner_row">
    <div class="col13">
      {{created_by_name}}
    </div>
    <div class="col4 last">
      {{created}}
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col16">
      <div>
        {{text}}
      </div>
    </div>
  </div>
  <div class="inner_row show{{post_editable}}">
    <div class="col1 last right">
      <div class="button" onclick="location.href='/forum/post_edit?id={{id}}';">
        <i class="fa-solid fa-pencil"></i>
      </div>
    </div>
  </div>
</div>

<div class="right">
  <div class="button forum_post_new" onclick="location.href='/forum/post_new?topic_id={{topic_id}}'">Beitrag erstellen</div>
</div>

<script>
  forum_topic_init();
</script>