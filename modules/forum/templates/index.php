<?php
$PROPERTIES['pathbar']=array(
  ''=>'Forum'
);
?>

<div id="TMPL_FORUM_ROW" class="row forum" style="display:none">
  <div class="inner_row">
    <div class="col13">
      <b><a href="/forum/forum?id={{id}}">{{forum_name}}</a></b>
    </div>
  </div>
  <div id="TMPL_FORUM_ROW-{{id}}-SUB" class="inner_row mt0_5" class="row" style="display:none">
    <div class="col13">
      <div>
        <a href="/forum/topic?id={{topic_id}}">{{topic_name}}</a>
      </div>
    </div>
    <div class="col2">
      <span class="smaller">{{posts_count}} {{posts_label}}</span>
    </div>
    <div class="col3 right">
      <span class="smaller">{{max_created}}</span>
    </div>
  </div>
  <div class="inner_row">
    <div class="col13 mt0_5">
      <span class="smaller">{{more_count_label}}</span>
    </div>
  </div>
</div>


<script>
  forum_init();
</script>