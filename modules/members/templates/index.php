<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration',''=>'Mitglieder');
$PROPERTIES['body_class']='header_h5';
?>

<?php foreach($members as $member): ?>
  <div class="row">
    <div class="col8">
      <div>
        <div>Mitglied: <?php echo $member->name ?></div>
      </div>
    </div>
    <div class="col4">
      <div>
        <div>Abholer: <?php echo $member->consumer?'ja':'nein' ?></div>
      </div>
    </div>
    <div class="col4">
      <div>
        <div>Lieferant: <?php echo translate_supplier($member->producer) ?></div>
      </div>
    </div>
    <?php if(!empty($users[$member->id])): ?>
      <div class="inner_row mt1">
        <div class="col8">
          <div>
            <div>Benutzer</div>
          </div>
        </div>
        <div class="col8">
          <div>
            <div>Email</div>
          </div>
        </div>
      </div>
      <?php foreach($users[$member->id] as $user): ?>
        <div class="inner_row">
          <div class="col8">
            <div>
              <div><?php echo $user->name ?></div>
            </div>
          </div>
          <div class="col8">
            <div>
              <div><?php echo $user->email ?></div>
            </div>
          </div>
          <?php /*
          <div class="col5">
            <div>
              <?php foreach($user['access'] as $access=>$start_end): ?>
                <div>
                  <?php echo translate_access($access) ?> <?php echo translate_access_start($start_end['start']) ?> <?php echo translate_access_end($start_end['end']) ?>
                </div>
              <?php endforeach ?>
            </div>
          </div>
          */ ?>
        </div>
      <?php endforeach ?>
    <?php endif ?>
    <div class="col1 right last">
      <span class="button large" onclick="location.href='/members/edit?member_id=<?php echo $member->id ?>';">
        <i class="fa-solid fa-pencil"></i>
      </span>
    </div>
  </div>
<?php endforeach ?>

<div class="button" onclick="if(confirm('Neues Mitglied anlegen?')){location.href='/members/new';}">Neues Mitglied</div>