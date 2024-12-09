<?php
$PROPERTIES['pathbar']=array(
  '/admin' => 'Administration',
  '/users' => 'Benutzer',
  '' => $user['name']
);
?>

<div class="row">
  <div class="inner_row">
    <div class="col3">
      <div>
        <div>Name</div>
      </div>
    </div>
    <div class="col5">
      <div>
        <div><?php
          echo html_input(array(
            'field' => 'name',
            'type' => 'input_text',
            'info' => 'Benutzer Vor-/Nachmane',
            'url' => '/users/update_ajax?user_id='.$user['id'],
            'value' => $user['name']
          ));
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col3">
      <div>
        <div>E-Mail</div>
      </div>
    </div>
    <div class="col5">
      <div>
        <div><?php
          echo html_input(array(
            'field' => 'email',
            'type' => 'input_text',
            'info' => 'Benutzer E-Mailadresse',
            'url' => '/users/update_ajax?user_id='.$user['id'],
            'value' => $user['email']
          ));
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col3">
      <div>
        <div>Abhol-PIN</div>
      </div>
    </div>
    <div class="col5">
      <div>
        <div><?php
          echo html_input(array(
            'field' => 'pickup_pin',
            'type' => 'input_text',
            'info' => 'Benutzer Tür/Waage PIN',
            'url' => '/users/update_ajax?user_id='.$user['id'],
            'value' => $user['pickup_pin']
          ));
          ?>
        </div>
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
  <div class="inner_row mt1">
    <!--<div class="button" onclick="if(confirm('Wirklich weiteres Recht hinzufügen?')){location.href='/users/create_access?user_id=<?php echo $user['id'] ?>';}">Recht hinzufügen</div>-->
  </div>
  <div class="col1 right last">
    <div class="buttons">
      <div class="button large ok" onclick="active_input_post_value_goto('/users/?user_id=<?php echo $user['id'] ?>')">
        <i class="fa-solid fa-check"></i>
      </div>
    </div>
  </div>
</div>

