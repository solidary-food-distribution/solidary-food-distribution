<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('debits');

require_once('debits.class.php');

function execute_index(){
  $month = get_request_param('month');
  if($month == ''){
    $month = date('Y-m');
  }

  update_debits();

  $months = array();
  $mindate = date('Y-m-d', strtotime('-13 MONTHS'));
  $date = date('Y-m-d');
  while($date > $mindate){
    $months[substr($date, 0, 7)] = translate_month(substr($date, 5, 2)).' '.substr($date, 0, 4);
    $date = date('Y-m-d', strtotime('-1 MONTHS', strtotime($date)));
  }

  $month_prev = date('Y-m', strtotime('-1 MONTHS', strtotime($month.'-01')));
  $month_next = date('Y-m', strtotime('+1 MONTHS', strtotime($month.'-01')));
  if($month_next > date('Y-m')){
    $month_next = '';
  }

  $return = get_debits_data($month);
  $return['month'] = $month;
  $return['month_prev'] = $month_prev;
  $return['month_next'] = $month_next;
  $return['months'] = $months;
  return $return;
}


function execute_export_csv(){
  $month = get_request_param('month');
  $now = time();
  $data = get_debits_data($month);
  foreach($data['debits'] as $member_id => $debits){
    foreach($debits as $debit){
      if($debit->status == 'o'){
        $debit->update(array(
          'status' => 'e',
          'exported' => date('Y-m-d H:i:s', $now)
        ));
      }
    }
  }
  $data['now'] = $now;
  $data['month'] = $month;
  return $data;
}

function get_debits_data($month){
  require_once('pickups.class.php');
  $pickups = new Pickups(array('created' => $month.'%'));

  $ds = new Debits(array('pickup_id' => $pickups->keys()));
  $debits = array();
  $member_ids = array();
  foreach($ds as $d){
    $debits[$d->member_id][$d->id] = $d;
    $member_ids[$d->member_id] = 1;
  }

  require_once('members.class.php');
  $members = new Members(array('id' => array_keys($member_ids)));

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