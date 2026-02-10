<?php
$PROPERTIES['pathbar']=array(
  '/forum'=>'Forum',
  '/forum/forum?id={{forum_id}}'=>'{{forum_name}}',
  ''=>'{{topic_name}}',
);
?>

<div class="row forum ready_display" data-display="flex" style="display:none">
  <div class="inner_row">
    <div class="col16">
      <b>Thema bearbeiten in Forum '{{forum_name}}'</b>
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col2">
      Thema
    </div>
    <div class="col12">
      <input type="text" id="topic_name" onkeyup="forum_save_input(this, 'topic_edit', '{{topic_id}}')" value="{{topic_name}}" />
    </div>
  </div>
  <div class="inner_row">
    <div class="col5 last right">
      <div class="button" onclick="forum_topic_edit_post()">Speichern</div>
    </div>
  </div>
</div>


<script>
  forum_topic_edit_init();
</script>