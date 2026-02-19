<?php
$PROPERTIES['body_class']='footer_h4';
?>

<?php if(!empty($message)): ?>
  <div class="row">
    <?php echo htmlentities($message) ?>
  </div>
<?php endif ?>

<?php if(!$deactivated): ?>

<?php if(!empty($forum_posts)): ?>
  <div class="row" id="forum_posts">
    <div class="inner_row">
      <div class="col18"><b>Neue Forum Beiträge <?php echo $_SESSION['last_login']!='0000-00-00 00:00:00'?'seit '.date('d.m.Y H:i', strtotime($_SESSION['last_login'])):''; ?></b></div>
    </div>
    <?php foreach($forum_posts as $forum_topics): ?>
      <div class="inner_row mt0_5">
        <div class="col18"><b><a href="/forum/forum?id=<?php echo $forum_topics[key($forum_topics)]['forum_id'] ?>"><?php echo htmlentities($forum_topics[key($forum_topics)]['forum_name']) ?></a></b></div>
      </div>
      <?php
        $topic_count=0;
        $topic_end=0;
      ?>
      <?php foreach($forum_topics as $forum_topic): ?>
        <?php
          $post_label='Beitrag';
          $last_label='am';
          if($forum_topic['count_posts']>1){
            $post_label='Beiträge';
            $last_label='zuletzt';
          }
          $topic_count++;
          if($topic_count>3){
            $topic_end = 1;
            $topic_count_more = count($forum_topics)-$topic_count + 1;
            if($topic_count_more==1){
              $topic_count_more_label='weiteres Thema';
            }else{
              $topic_count_more_label='weitere Themen';
            }
          }
        ?>
        <?php if($topic_end): ?>
          <div class="inner_row">
            <div class="col8">
              <span class="smaller">(<?php echo $topic_count_more.' '.$topic_count_more_label ?> mit neuen Beiträgen)</small>
            </div>
          </div>
          <?php break; ?>
        <?php endif ?>
        <div class="inner_row">
          <div class="col11"><a href="/forum/topic?id=<?php echo $forum_topic['topic_id'] ?>#post<?php echo $forum_topic['min_post_id'] ?>"><?php echo htmlentities($forum_topic['topic_name']) ?></a></div>
          <div class="col2 right"><span class="smaller"><?php echo $forum_topic['count_posts'].' '.$post_label ?></span></div>
          <div class="col5 right"><span class="smaller"><?php echo $last_label.' '.date('d.m.Y H:i', strtotime($forum_topic['max_created'])) ?></span></div>
        </div>
      <?php endforeach ?>
    <?php endforeach ?>
    <div class="inner_row">
      <div class="col1 right last">
        <span class="button" onclick="start_forum_read()">
          <i class="fa-solid fa-check"></i>
        </span>
      </div>
    </div>
  </div>
<?php endif //forum_posts ?>

<?php /*
<?php foreach($infos as $info): ?>
  <div class="row" id="info<?php echo $info->id ?>">
    <div class="inner_row">
      <div class="col4">
        <?php if($info->published == '0000-00-00 00:00:00'): ?>
          Entwurf<br>
          <?php echo format_date($info->created) ?>
        <?php else: ?>
          <b><?php echo format_date($info->published) ?></b>
        <?php endif ?>
      </div>
      <div class="col13">
        <b><?php echo htmlentities($info->subject) ?></b>
      </div>
    </div>
    <div class="inner_row">
      <div class="col4"></div>
      <div class="col13 mt0_5">
        <div>
          <?php echo format_content($info->content) ?>
        </div>
      </div>
      <div class="col1 right last" style="position:relative">
        <span class="button" style="position:absolute;bottom:0px;" onclick="start_info_read(<?php echo $info->id ?>);">
          <i class="fa-solid fa-check"></i>
        </span>
      </div>
    </div>
  </div>
<?php endforeach ?>
*/ ?>


<div class="selection">
  <div class="item" onclick="location.href='/order'">
    <span class="label">Bestellen</span>
  </div>
  <?php if(user_has_access('deliveries') || user_has_access('inventory')): ?>
    <div class="item" onclick="location.href='/start/store'">
      <span class="label">Abholraum</span>
    </div>
  <?php elseif(user_has_access('pickups')): ?>
    <div class="item" onclick="location.href='/pickups'">
      <span class="label">Abholungen</span>
    </div>
  <?php endif ?>
  <div class="item" onclick="location.href='/activities'">
    <span class="label">Aktivitäten</span>
  </div>
  <div class="item" onclick="location.href='/forum'">
    <span class="label">Forum</span>
  </div>
  <div class="item" onclick="location.href='/settings'">
    <span class="label">Einstellungen</span>
  </div>
  <?php if(user_has_access('admin')): ?>
    <div class="item" onclick="location.href='/admin'">
      <span class="label">Administration</span>
    </div>
  <?php endif ?>
</div>

<?php endif //deactivted? ?>

<?php ob_start(); ?>
<div class="row center">
  Version: <a href='/start/version'>%VERSION%</a>
</div>
<?php
  $PROPERTIES['footer']=ob_get_clean();
?>
