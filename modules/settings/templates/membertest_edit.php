<?php
$PROPERTIES['pathbar']=array(
  '/settings'=>'Einstellungen',
  '/settings/membertest'=>'Patenschaften',
  '' => $member->name
);
?>

<div class="row">
  <div class="inner_row">
    <div class="col3">
      <div>Name</div>
    </div>
    <div class="col12">
      <?php
        echo html_input(array(
          'field' => 'name',
          'type' => 'input_text',
          'info' => 'Test-Mitglied Vor-/Nachmane',
          'url' => '/settings/membertest_update_ajax?member_id='.$member->id,
          'value' => $member->name
        ));
        ?>
    </div>
  </div>
  <div class="inner_row">
    <div class="col3">
      <div>E-Mail</div>
    </div>
    <div class="col12">
      <?php
        echo html_input(array(
          'field' => 'email',
          'type' => 'input_text',
          'info' => 'Test-Mitglied E-Mailadresse',
          'url' => '/settings/membertest_update_ajax?member_id='.$member->id,
          'value' => $muser->email
        ));
        ?>
    </div>
  </div>
  <?php /*
  <div class="inner_row">
    <div class="col3">
      <div>Bestellen bis</div>
    </div>
    <div class="col3">
      <?php
        echo html_input(array(
          'field' => 'deactivate_on',
          'type' => 'input_text',
          'info' => 'Bestellen bis',
          'url' => '/settings/membertest_update_ajax?member_id='.$member->id,
          'value' => date('d.m.Y',strtotime($member->deactivate_on))
        ));
        ?>
    </div>
    <div class="col5">
      Datum als TT.MM.JJJJ
    </div>
  </div>
  */ ?>
  <?php /*
  <div class="inner_row">
    <div class="col3">
      <div>Bestellgrenze</div>
    </div>
    <div class="col3">
      <?php
        echo html_input(array(
          'field' => 'order_limit',
          'class' => 'right',
          'type' => 'input_text',
          'info' => 'Bestellen bis',
          'url' => '/settings/membertest_update_ajax?member_id='.$member->id,
          'value' => intval($member->order_limit)
        ));
        ?>
    </div>
    <div class="col5">
      EUR/Woche
    </div>
  </div>
  */ ?>
  <div class="col1 right last">
    <div class="buttons">
      <div class="button large ok" onclick="active_input_post_value_goto('/settings/membertest?member_id=<?php echo $member->id ?>')">
        <i class="fa-solid fa-check"></i>
      </div>
    </div>
  </div>
</div>

