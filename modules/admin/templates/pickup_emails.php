<?php
$PROPERTIES['pathbar']=array('/admin'=>'Administration','/admin/orders?date='.$date => 'Mitglieder Bestellung',''=>'Abholende E-Mails');
?>

<div class="row">
  <textarea style="width:100%;" rows="30">
    <?php 
      $index=0;
      $list='';
      foreach($users as $user){
        if(strpos(' '.$user->name,'GEKÜNDIGT') || strpos(' '.$user->email,'GEKÜNDIGT') || !strpos($user->email,'@')){
          continue;
        }
        $list.=$user->name.' <'.$user->email.'>, ';
      }
      echo htmlentities(trim($list,', '));
    ?>
  </textarea>
</div>