<?php
$PROPERTIES['pathbar']=array(
  '/forum'=>'Forum',
  '/forum/forum?id={{forum_id}}'=>'{{forum_name}}',
  ''=>'Neues Thema',
);
?>

<div class="row forum">
  <div class="inner_row">
    <div class="col16">
      <b>Neues Thema in Forum '{{forum_name}}'</b>
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col2">
      Thema
    </div>
    <div class="col12">
      <input type="text" id="topic_name" onkeyup="forum_save_input(this, 'topic_new', '{{forum_id}}')" />
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col2">
      Inhalt
    </div>
    <div class="col12">
      <textarea id="post_text" onkeyup="forum_save_input(this, 'topic_new', '{{forum_id}}')"></textarea>
    </div>
  </div>
  <div class="inner_row">
    <div class="col5 last right">
      <div class="button" onclick="forum_topic_new_post()">Thema starten</div>
    </div>
  </div>
</div>


<script>
  forum_topic_new_init();
</script>