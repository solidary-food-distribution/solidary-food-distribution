<?php
$PROPERTIES['pathbar']=array(
  '/admin' => 'Administration',
  '/members' => 'Mitglieder',
  '' => $member->name
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
            'info' => 'Mitglied Vor-/Nachmane',
            'url' => '/members/update_ajax?member_id='.$member->id,
            'value' => $member->name
          ));
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col3">
      <div>
        <div>Lieferant</div>
      </div>
    </div>
    <div class="col7">
      <div>
        <div><?php
          echo html_input(array(
            'field' => 'producer', 
            'type' => 'options',
            'info' => 'Hersteller',
            'url' => '/members/update_ajax?member_id='.$member->id,
            'value' => $member->producer,
            'options' => array('0' => 'nein', '1' => 'Erzeuger', '2' => 'Händler'),
          ));
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col3">
      <div>
        <div>Abholer</div>
      </div>
    </div>
    <div class="col7">
      <div>
        <div><?php
          echo html_input(array(
            'field' => 'consumer', 
            'type' => 'options',
            'info' => 'Abholer',
            'url' => '/members/update_ajax?member_id='.$member->id,
            'value' => $member->consumer,
            'options' => array('0' => 'nein', '1' => 'ja'),
          ));
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="inner_row mt1">
    <div class="col8">
      <div>
        <div>Benutzer Vor-/Nachmane</div>
      </div>
    </div>
    <div class="col8">
      <div>
        <div>E-Mail / Login</div>
      </div>
    </div>
  </div>
  <?php foreach($users[$member->id] as $user): ?>
    <div class="inner_row">
      <div class="col8">
        <?php 
          echo html_input(array(
            'field' => 'name', 
            'type' => 'input_text',
            'info' => 'Benutzer Vor-/Nachmane',
            'url' => '/members/update_user_ajax?user_id='.$user->id,
            'value' => $user->name
          ));
        ?>
      </div>
      <div class="col8">
        <?php 
          echo html_input(array(
            'field' => 'email', 
            'type' => 'input_text',
            'info' => 'Benutzer E-Mail / Login',
            'url' => '/members/update_user_ajax?user_id='.$user->id,
            'value' => $user->email
          ));
        ?>
      </div>
    </div>
  <?php endforeach ?>
  <div class="inner_row">
    <div class="button" onclick="if(confirm('Wirklich weiteren Benutzer hinzufügen?')){location.href='/members/create_user?member_id=<?php echo $member->id ?>';}">Weiteren Benutzer hinzufügen</div>
  </div>
  <div class="col1 right last">
    <div class="buttons">
      <div class="button large ok" onclick="active_input_post_value_goto('/members/?member_id=<?php echo $member->id ?>')">
        <i class="fa-solid fa-check"></i>
      </div>
    </div>
  </div>
</div>

