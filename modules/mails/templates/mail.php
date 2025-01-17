<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  '/mails'=>'E-Mails',
  '' => $mail->subject,
);
$PROPERTIES['body_class']='header_h5';
?>

<div class="row">
  <div class="inner_row">
    <div class="col2">
      An
    </div>
    <div class="col8">
      <?php echo html_input(array(
        'type' => 'input_text',
        'url' => '/mails/update_ajax?mail_id='.$mail->id,
        'field' => 'to',
        'value' => $mail->to,
      )) ?>
    </div>
    <div class="col4">
      <input type="checkbox" onclick="mails_to_all_users(this)" <?php echo ($mail->to=='[ALL_USERS]')?'checked':'' ?> /> Alle Nutzer
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col2">
      Betreff
    </div>
    <div class="col12">
      <?php echo html_input(array(
        'type' => 'input_text',
        'url' => '/mails/update_ajax?mail_id='.$mail->id,
        'field' => 'subject',
        'value' => $mail->subject,
      )) ?>
    </div>
  </div>
  <div class="inner_row mt0_5">
    <div class="col2">
      Inhalt
    </div>
    <div class="col12">
      <textarea class="input" style="width:100%;text-align:left;" rows="8" data-type="input_text" data-field="content" data-url="/mails/update_ajax?mail_id=<?php echo $mail->id ?>" onclick="input_onfocus(this)" onblur="input_text_onchange(this)" onchange="input_text_onchange(this)"><?php echo htmlentities($mail->content) ?></textarea>
    </div>
  </div>
  <div class="inner_row">
    <div class="col4 last right">
      <div class="button" onclick="mails_send('<?php echo $mail->id ?>')">
        <?php if($mail->sent == '0000-00-00 00:00:00'): ?>
          Senden
        <?php else: ?>
          Erneut senden
        <?php endif ?>
      </div>
    </div>
  </div>
</div>
