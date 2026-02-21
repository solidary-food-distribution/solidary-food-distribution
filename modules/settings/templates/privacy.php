<?php
$PROPERTIES['pathbar']=array('/settings'=>'Einstellungen', '' => 'Privatsphäre');

?>
<div class="row">
  <div class="inner_row">
    <div class="col6">
      <div class="title">Forum</div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col6">
      <div>Benutzer Namensanzeige</div>
    </div>
    <div class="col10">
      <div><?php
        echo html_input(array(
          'field' => 'forum_name',
          'type' => 'input_text',
          'info' => '',
          'url' => '/settings/privacy_update_field_ajax',
          'value' => $forum_name
        ));
        ?>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="inner_row">
    <div class="col6">
      <div class="title">Mitgliederkarte</div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col6">
      <div>Mitglied Namensanzeige</div>
    </div>
  </div>
  <?php
    $privacy_membermap_settings = array(
      'anonym' => 'anonym',
      'first_letters' => 'Erste Buchstaben Vorname und Nachname: <i>'.htmlentities($membermap_name['first_letters']).'</i>',
      'given_name' => 'Vorname und erster Buchstabe Nachname: <i>'.htmlentities($membermap_name['given_name']).'</i>',
      'given_name_street' => 'Vorname und erster Buchstabe Nachname und Straße ohne Nr: <i>'.htmlentities($membermap_name['given_name_street']).'</i>',
    );
  ?>
  <?php foreach($privacy_membermap_settings as $value => $label_html): ?>
    <div class="inner_row">
      <div class="col14">
        <input type="radio" name="membermap_name" value="<?php echo htmlentities($value) ?>" <?php echo ($settings['membermap_name']==$value?'checked="checked"':'') ?> onchange="privacy_update(this)" />
        <?php echo $label_html ?>
      </div>
    </div>
  <?php endforeach ?>
</div>