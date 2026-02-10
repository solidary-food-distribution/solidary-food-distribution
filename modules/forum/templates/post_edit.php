<?php
$PROPERTIES['pathbar']=array(
  '/forum'=>'Forum',
  '/forum/forum?id={{forum_id}}'=>'{{forum_name}}',
  '/forum/topic?id={{topic_id}}'=>'{{topic_name}}',
  ''=>'Beitrag bearbeiten'
);
?>

<div class="row forum">
  <div class="inner_row">
    <div class="col16">
      <b>Beitrag bearbeiten in Forum '{{forum_name}}' Thema '{{topic_name}}'</b>
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col17">
      <textarea id="post_text" onkeyup="forum_save_input(this, 'post_edit', '{{id}}')">{{text}}</textarea>
    </div>
  </div>
  <div class="inner_row">
    <div class="col5 last right">
      <div class="button" onclick="forum_post_edit_post()">Beitrag speichern</div>
    </div>
  </div>
</div>


<script>
  forum_post_edit_init();
</script>