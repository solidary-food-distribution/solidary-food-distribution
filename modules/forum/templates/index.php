<?php
$PROPERTIES['pathbar']=array(
  ''=>'Forum'
);
?>

<div id="TMPL_FORUM_ROW" class="row" style="display:none">
  <div class="inner_row">
    <div class="col13">
      <b><a href="/forum/forum?id={{id}}">{{name}}</a></b>
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col13">
      <div>
        {{latest_topic}}
      </div>
    </div>
    <div class="col4">
      {{latest_date}}
    </div>
  </div>
</div>


<script>
  forum_init();
</script>