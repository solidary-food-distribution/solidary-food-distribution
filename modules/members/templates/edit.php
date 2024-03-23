<?php
$PROPERTIES['pathbar']=array(
  '/admin' => 'Administration',
  '/members' => 'Mitglieder',
  '' => 'Mitglied-Nr '.$member->identification
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
            'type' => 'string',
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
        <div>Mitglied-Nr</div>
      </div>
    </div>
    <div class="col5">
      <div>
        <div><?php
          echo html_input(array(
            'field' => 'identification', 
            'type' => 'string',
            'info' => 'Mitgliednummer',
            'url' => '/members/update_ajax?member_id='.$member->id,
            'value' => $member->identification
          ));
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col3">
      <div>
        <div>Hersteller</div>
      </div>
    </div>
    <div class="col5">
      <div>
        <div><?php
          echo html_input(array(
            'field' => 'producer', 
            'type' => 'options',
            'info' => 'Hersteller',
            'url' => '/members/update_ajax?member_id='.$member->id,
            'value' => $member->producer,
            'options' => array('1' => 'ja', '0' => 'nein'),
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
    <div class="col5">
      <div>
        <div><?php
          echo html_input(array(
            'field' => 'consumer', 
            'type' => 'options',
            'info' => 'Abholer',
            'url' => '/members/update_ajax?member_id='.$member->id,
            'value' => $member->consumer,
            'options' => array('1' => 'ja', '0' => 'nein'),
          ));
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col1 right last">
    <div class="buttons">
      <div class="button large ok" onclick="active_input_post_value_goto('/members/?member_id=<?php echo $member->id ?>')">
        <i class="fa-solid fa-check"></i>
      </div>
    </div>
  </div>
</div>

