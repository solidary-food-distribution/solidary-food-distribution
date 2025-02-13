<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('debits');

require_once('debits.class.php');

function execute_index(){
  update_debits();
  return get_debits_data();
}


function execute_export_csv(){
  $now = time();
  $data = get_debits_data();
  foreach($data['debits'] as $member_id => $debits){
    foreach($debits as $debit){
      $debit->update(array(
        'status' => 'e',
        'exported' => date('Y-m-d H:i:s', $now)
      ));
    }
  }
  $data['now'] = $now;
  return $data;
}

function get_debits_data(){
  $ds = new Debits(array('status' => 'o'));
  $debits = array();
  $member_ids = array();
  $pickup_ids = array();
  foreach($ds as $d){
    $debits[$d->member_id][$d->id] = $d;
    $member_ids[$d->member_id] = 1;
    $pickup_ids[$d->pickup_id] = 1;
  }
  #logger(print_r($ds,1));

  require_once('members.class.php');
  $members = new Members(array('id' => array_keys($member_ids)));

  require_once('pickups.class.php');
  $pickups = new Pickups(array('id' => array_keys($pickup_ids)));

  return array('members' => $members, 'debits' => $debits, 'pickups' => $pickups);
}

function update_debits(){
  require_once('pickups.class.php');
  $pickups = new Pickups(array('status' => array('a', 'c')));
  if(!count($pickups)){
    return;
  }
  $pickup_ids = $pickups->keys();
  #logger("update_debits pickup_ids ".print_r($pickup_ids,1));

  $debits = new Debits(array('pickup_id' => $pickup_ids));
  $existing = array();
  foreach($debits as $debit){
    $existing[] = $debit->pickup_id;
  }
  #logger("update_debits existing ".print_r($existing,1));

  $missing = array_diff($pickup_ids, $existing);
  #logger("update_debits missing ".print_r($missing,1));
  foreach($missing as $pickup_id){
    $pickup = $pickups[$pickup_id];
    $qry = "SELECT tax, SUM(price_sum) amount FROM msl_pickup_items WHERE pickup_id='".intval($pickup_id)."' GROUP BY tax";
    $amounts = SQL::selectKey2Val($qry, 'tax', 'amount');
    foreach($amounts as $tax => $amount){
      if(!round($amount,2)){
        continue;
      }
      $debit = Debit::create($pickup->member_id);
      $debit->update(array(
        'pickup_id' => $pickup->id,
        'tax' => $tax,
        'amount' => $amount,
      ));
    }
  }
}