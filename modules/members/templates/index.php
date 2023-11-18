<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration',''=>'Mitglieder');
$PROPERTIES['body_class']='header_h5';
?>

<?php ob_start(); ?>
<div class="controls">
  <div class="control">
    <span class="label" onclick="location.href='/members/new'">Neues Mitglied</span>
  </div>
</div>
<?php $PROPERTIES['header']=ob_get_clean(); ?>

<?php foreach($members as $member_id=>$member): ?>
  <div class="row">
    <div class="inner_row mb1">
      <div class="col5">
        <div>
          <div>Name: <?php echo $member['name'] ?></div>
          <div>Mitglied-Nr: <?php echo $member['identification'] ?></div>
        </div>
      </div>
      <div class="col5">
        <div>
          <div>Hersteller: <?php echo $member['producer']?'ja':'nein' ?></div>
          <div>Abholer: <?php echo $member['consumer']?'ja':'nein' ?></div>
        </div>
      </div>
    </div>
    <div class="inner_row">
      <div class="col5">
        <div>
          <div>Benutzerrechte</div>
        </div>
      </div>
    </div>
    <?php foreach($member['access_users'] as $user_id=>$user): ?>
      <div class="inner_row mb1">
        <div class="col5">
          <div>
            <div>Name: <?php echo $user['name'] ?></div>
            <div>E-Mail: <?php echo $user['email'] ?></div>
          </div>
        </div>
        <div class="col5">
          <div>
            <?php foreach($user['access'] as $access=>$start_end): ?>
              <div>
                <?php echo translate_access($access) ?> <?php echo translate_access_start($start_end['start']) ?> <?php echo translate_access_end($start_end['end']) ?>
              </div>
            <?php endforeach ?>
          </div>
        </div>
      </div>
    <?php endforeach ?>
    <div class="inner_row">
      <div class="col right last">
        <div>
          <div class="button" onclick="alert('NOCH NICHT UMGESETZT');">Bearbeiten</div>
        </div>
      </div>
    </div>
  </div>
<?php endforeach ?>

