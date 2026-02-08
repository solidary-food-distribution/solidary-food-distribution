<?php
$PROPERTIES['pathbar']=array(
  '/forum'=>'Forum',
  '/forum/forum?id={{forum_id}}'=>'{{forum_name}}',
  ''=>'{{topic_name}}',
);
?>

<div class="row ready_display_block">
  <div class="col13">
    <b>{{topic_name}}</b>
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
</div>

<div class="right">
  <div class="button forum_post_new" onclick="location.href='/forum/post_new?topic_id={{topic_id}}'">Beitrag erstellen</div>
</div>

<script>
  forum_topic_init();
</script>