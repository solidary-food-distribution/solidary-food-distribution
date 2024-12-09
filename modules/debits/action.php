<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('debits');

require_once('debits.class.php');

function execute_index(){

}


function update_debits(){
  require_once('pickups.class.php');
  $pickups = new Pickups(array('status' => array('a', 'c')));
  if(!count($pickups)){
    return;
  }
  $pickup_ids = $pickups->keys();
  $debits = new Debits(array('pickup_id' => $pickup_ids));
  $debit_ids = $debits->keys();
  $missing = array_diff($pickup_ids, $debit_ids);
  foreach($missing as $pickup_id){
    $pickup = $pickups[$pickup_ids];
    $debit = Debit::create($pickup->member_id);
    $debit->update(array(
      'pickup_id' => $pickup->id,
      'amount' => 
  }
}