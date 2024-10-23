<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration','/users'=>'Benutzer',''=>'Benutzer E-Mails');
?>

<div class="row">
  <?php 
    $index=0;
    $list='';
    foreach($users as $user){
      if(strpos(' '.$user['name'],'GEKÜNDIGT') || strpos(' '.$user['email'],'GEKÜNDIGT') || !strpos($user['email'],'@')){
        continue;
      }
      $list.=$user['name'].' <'.$user['email'].'>, ';
    }
    echo htmlentities(trim($list,', '));
  ?>
</div>