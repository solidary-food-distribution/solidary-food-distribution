<?php

$filename = '/tmp/debits_'.date('Y-m-d-H-i-s', $now).'.csv';

$h = fopen($filename, 'w');
$header = array(
  'Name',
  'Steuersatz',
  'Summe inkl Steuer',
  'Betreff',
);
fputcsv($h, $header, ';');

foreach($members as $member_id => $member){
  $sum = array();
  $subject = 'Abholung ';
  foreach($debits[$member_id] as $debit_id => $debit){
    $sum[$debit->tax] += round($debit->amount, 2);
    $date = date('d.m.Y', strtotime($pickups[$debit->pickup_id]->created));
    if(!strpos($subject, $date)){
      $subject .= $date.' ';
    }
  }
  foreach($sum as $tax => $amount){
    if(!round($amount, 2)){
      continue;
    }
    $data = array(
      $member->name,
      str_replace('.', ',', round($tax, 2)),
      str_replace('.', ',', round($amount, 2)),
      trim($subject),
    );
    fputcsv($h, $data, ';');
  }
}
fclose($h);

header('Content-Description: File Transfer'); 
header('Content-Type: text/csv; charset=UTF-8'); 
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
header("Cache-control: private");
readfile($filename);

system("rm ".escapeshellarg($filename));
exit;