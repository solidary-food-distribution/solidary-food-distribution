<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  '/admin/infos'=>'Infos',
  '' => format_date(($info->published=='0000-00-00 00:00:00'?$info->created:$info->published)).' '.$info->subject
);
?>

<div class="row">
  <div class="inner_row">
    <div class="col4">
      Status
    </div>
    <div class="col13">
      <div><?php
        $status = ($info->published=='0000-00-00 00:00:00'?'0':'1');
        echo html_input(array(
          'field' => 'status', 
          'type' => 'options',
          'url' => '/admin/info_ajax?info_id='.$info->id,
          'value' => $status,
          'options' => array('0' => 'Entwurf', '1' => 'Sichtbar'),
        ));
        ?>
      </div>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4">
      Betreff
    </div>
    <div class="col13">
      <?php echo html_input(array(
        'type' => 'input_text',
        'url' => '/admin/info_ajax?info_id='.$info->id,
        'field' => 'subject',
        'value' => $info->subject,
      )) ?>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4">Inhalt</div>
    <div class="col13">
      <textarea class="input" style="width:100%;text-align:left;" rows="8" data-type="input_text" data-field="content" data-url="/admin/info_ajax?info_id=<?php echo $info->id ?>" onclick="input_onfocus(this)" onblur="input_text_onchange(this)" onchange="input_text_onchange(this)"><?php echo htmlentities($info->content) ?></textarea>
    </div>
  </div>
</div>
