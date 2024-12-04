<?php

$supplier_name = $supplier->name;
$supplier_name = iconv('UTF-8', 'ASCII//translit', $supplier_name);
$supplier_name = str_replace(' ', '_', $supplier_name);
$supplier_name = strtolower($supplier_name);


$filename = '/tmp/'.$pickup_date.'_'.$supplier_name.'.csv';

$h = fopen($filename, 'w');
$header = array(
  'ArtNr',
  'Name',
  'Benötigt',
  'Einheit',
  'Bestellen',
  'Einheit',
  'Einzelpreis',
  'Einheit',
  'Summe',
  'Steuersatz',
  'Summe Brutto'
);
fputcsv($h, $header, ';');

foreach($product_sums[$supplier->id] as $oi_sum){
  $data = array(
    $oi_sum['supplier_product_id'],
    $oi_sum['name'],
    str_replace('.', ',', $oi_sum['amount_needed']),
    $oi_sum['amount_needed_unit'],
    str_replace('.', ',', $oi_sum['amount_order']),
    $oi_sum['amount_order_unit'],
    format_money($oi_sum['price']),
    $oi_sum['price_unit'],
    format_money($oi_sum['sum_price']),
    str_replace('.', ',', $oi_sum['tax']),
    format_money($oi_sum['sum_price'] * (100 + $oi_sum['tax'])/100)
  );
  fputcsv($h, $data, ';');
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