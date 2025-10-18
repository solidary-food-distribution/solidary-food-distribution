<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration',''=>'Benutzer');
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
  <div class="controls">
    <div class="button" onclick="location.href='/users/emails';">Benutzer E-Mails</div>
    <div class="button" onclick="location.href='/users/pins';">Türcodes</div>
  </div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($users as $user_id=>$user): ?>
  <div class="row">
    <?php if(isset($members[$user['member_id']]->pate_id) && $members[$user['member_id']]->pate_id): ?>
      <div class="inner_row">
        <div class="col12">
          Patenschaft von <?php echo $members[$members[$user['member_id']]->pate_id]->name ?>
        </div>
      </div>  
    <?php endif ?>
    <div class="inner_row">
      <div class="col12">
        <div>
          <div>Name: <?php echo $user['name'] ?></div>
          <div>E-Mail: <?php echo $user['email'] ?></div>
        </div>
      </div>
    </div>
    <?php foreach($user['access'] as $member_id=>$member_access): ?>
      <div class="inner_row mt1">
        <div class="col12">
          <div>
            <?php if($member_id): ?>
              Mitglied <?php echo $member_access['name']; ?>
            <?php else: ?>
              Administration
            <?php endif ?>
          </div>
        </div>
      </div>
      <?php foreach($member_access['access'] as $access=>$start_end): ?>
        <div class="inner_row">
          <div class="col5">
            <div><?php echo translate_access($access) ?> <?php echo translate_access_start($start_end['start']) ?> <?php echo translate_access_end($start_end['end']) ?></div>
          </div>
        </div>
      <?php endforeach ?>
    <?php endforeach ?>

<!--
    <?php foreach(array('members','users','products','orders','debits') as $access): ?>
      <div class="inner_row">
        <div class="col3">
          <div><?php echo translate_access($access) ?></div>
        </div>
        <div class="col1">
          <div><?php echo 'ja' ?></div>
        </div>
      </div>
    <?php endforeach ?>
-->

    <div class="col1 right last">
      <span class="button" onclick="location.href='/users/edit?user_id=<?php echo $user_id ?>';">
        <i class="fa-solid fa-pencil"></i>
      </span>
    </div>
  </div>
<?php endforeach ?>

