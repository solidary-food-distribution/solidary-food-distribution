<?php
$PROPERTIES['pathbar']=array(
  '/forum'=>'Forum',
  '/forum/forum?id={{forum_id}}'=>'{{forum_name}}',
  '/forum/topic?id={{topic_id}}'=>'{{topic_name}}',
);
?>

<div class="row forum">
  <div class="inner_row">
    <div class="col16">
      <b>Neuen Beitrag in Forum '{{forum_name}}' Thema '{{topic_name}}'</b>
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col2">
      Inhalt
    </div>
    <div class="col12">
      <textarea id="post_text" onkeyup="forum_save_input(this, 'post_new', '{{topic_id}}')"></textarea>
    </div>
  </div>
  <div class="inner_row">
    <div class="col5 last right">
      <div class="button" onclick="forum_post_new_post()">Beitrag erstellen</div>
    </div>
  </div>
</div>


<script>
  forum_post_new_init();
</script>