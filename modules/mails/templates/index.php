<?php
$PROPERTIES['pathbar']=array(
  '/admin'=>'Administration',
  '/mails'=>'E-Mails'
);
$PROPERTIES['body_class']='header_h5';
?>

<div class="button" onclick="if(confirm('Neue E-Mail anlegen?')){location.href='/mails/new';}">Neue E-Mail</div>

<?php foreach($mails as $mail): ?>
  <div class="row">
    <div class="col4">
      <?php echo format_date($mail->created) ?>
    </div>
    <div class="col8">
      <b><?php echo htmlentities($mail->subject) ?></b>
    </div>
    <div class="col5">
      <?php echo htmlentities($mail->to) ?>
    </div>
    <div class="col1 last">
      <span class="button" onclick="location.href='/mails/mail?mail_id=<?php echo $mail->id ?>';">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </span>
    </div>
  </div>
<?php endforeach ?>

