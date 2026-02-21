<?php
$PROPERTIES['pathbar']=array(
  '/forum'=>'Forum',
  ''=>'{{forum_name}}'
);
if(!isset($hide_topic_new)){
  $PROPERTIES['body_class']='header_h5';
}
?>

<?php if(!isset($hide_topic_new)): ?>
<?php ob_start(); ?>
  <div class="controls">
    <div class="button" onclick="location.href='/forum/topic_new?forum_id={{forum_id}}';">Neues Thema in '{{forum_name}}' starten</div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>
<?php endif ?>

<div id="TMPL_TOPIC_ROW" class="row forum forum_index" style="display:none">
  <div class="inner_row">
    <div class="col13">
      <b><a href="/forum/topic?id={{id}}#post{{latest_id}}">{{name}}</a></b>
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col13">
      <div>
        {{latest_text}}
      </div>
    </div>
    <div class="col2">
      <span class="smaller">{{posts_count}} {{posts_label}}</span>
    </div>
    <div class="col3 right">
      <span class="smaller">{{latest_date}}</span>
    </div>
  </div>
</div>


<script>
  forum_forum_init();
</script>