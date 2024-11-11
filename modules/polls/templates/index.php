<?php
$PROPERTIES['pathbar']=array(''=>'Umfragen');
$PROPERTIES['body_class']='header_h5';?>

<?php ob_start(); ?>
  <div class="controls">

  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($polls as $poll): ?>
  <div class="row">
    <div class="col12">
      <b><?php echo htmlentities($poll->title); ?></b>
    </div>
    <div class="col1 right last">
      <span class="button large" onclick="location.href='/polls/poll?poll_id=<?php echo $poll->poll_id ?>';">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </span>
    </div>
    </div>
  </div>
<?php endforeach ?>