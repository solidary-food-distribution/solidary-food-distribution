<?php

#500515,515041,515039,510511,302375,

function oekoring_download_bnns(){
  $ftp = ftp_connect('www.biooffice.de',21);
  if(!$ftp){
    return array('error' => 'connect to FTP failed');
  }
  $login = @ftp_login($ftp, '***OEKORING_FTP_USER***', '***OEKORING_FTP_PWD***');
  if(!$login){
    return array('error' => 'login to FTP failed');
  }
  ftp_pasv($ftp, true);
  $files = ftp_nlist($ftp, '.');
  foreach($files as $file){
    if(substr($file, -4, 4) == '.BNN'){
      $result = ftp_get($ftp, __DIR__.'/../data/oekoring/'.$file, $file, FTP_BINARY);
      if(!$result){
        return array('error' => 'download from FTP failed: '.error_get_last()['message']);
      }
    }
  }
  ftp_close($ftp);
  return array('result' => 'ok', 'files' => $files);
}

function oekoring_import_bnns(){
  $result = array();
  $files = glob(__DIR__.'/../data/oekoring/*.BNN');
  foreach($files as $file){
    $result[$file] = oekoring_import_bnn($file);
  }
  return $result;
}

function oekoring_import_bnn($file){
  $h = fopen($file, 'r');
  $header = fgetcsv($h, 9999, ';');
  array_unshift($header,''); //index = documentation index
  $prices_start = substr($header[8],0,4).'-'.substr($header[8],4,2).'-'.substr($header[8],6,2);
  $prices_end = substr($header[9],0,4).'-'.substr($header[9],4,2).'-'.substr($header[9],6,2);
  if(!empty($header[9]) && strtotime($prices_end.' 23:59:59') < time()){
    return 'too old';
  }

  $full = false;
  if(basename($file) == 'PL.BNN'){
    $full = true;
  }
  require_once('sql.class.php');
  if($full){
    SQL::update("UPDATE msl_products SET import_status='n' WHERE supplier_id='35'");
  }

  $brands = SQL::selectKey2Val("SELECT bnn,id FROM msl_brands", 'bnn', 'id');

  $products = array();
  $prices = array();
  $linenr = 0;
  while($line = fgetcsv($h, 9999, ';')){
    $linenr++;
    array_unshift($line,''); //index = documentation index
    if($line[1] == '' && $line[2] == '' && $line[3] == '99'){
      break; //end of file
    }
    $status = (strpos('ANWR',$line[2])!==false)?'o':'d';
    $name = $line[7];
    $brand_id = 0;
    $brand_bnn = iconv('CP850', 'UTF-8', $line[11]);
    if(isset($brands[$brand_bnn])){
      $brand_id = $brands[$brand_bnn];
    }
    $type = 'p';
    if($line[26]=='J'){
      $type = 'k';
    }else{
      $name .= ' '.$line[24];
    }
    $row = array(
      '35',     //supplier_id
      $line[1], //supplier_product_id
      iconv('CP850', 'UTF-8', $name),
      $type,      //type
      's', //status "searchable"
      $status, //import_status
      str_replace(',', '.', $line[23]), //amount_per_bundle
      $brand_id,
      $line[5], //gtin_piece
      $line[6], //gtin_bundle
    );
    $products[$linenr]=SQL::escapeArray($row);
    $start = $prices_start;
    $end = $prices_end;
    $promo = 0;
    if($line[62] == 'A'){
      $start = substr($line[63],0,4).'-'.substr($line[63],4,2).'-'.substr($line[63],6,2);
      $end = substr($line[64],0,4).'-'.substr($line[64],4,2).'-'.substr($line[64],6,2);
      $promo = 1;
    }
    if($line[34] == 1){
      $tax = 7.0;
    }elseif($line[34] == 2){
      $tax = 19.0;
    }else{
      return "tax unknown ".print_r($line,1);
    }
    $price = array(
      'start' => $start,
      'end' => $end,
      'purchase' => str_replace(',', '.', $line[38]), //price per piece excl tax
      'purchase_promo' => $promo,
      'tax' => $tax,
      'purchase_bulk1_amount' => floatval(str_replace(',', '.', $line[41])),
      'purchase_bulk1' => floatval(str_replace(',', '.', $line[42])),
      'purchase_bulk2_amount' => floatval(str_replace(',', '.', $line[45])),
      'purchase_bulk2' => floatval(str_replace(',', '.', $line[46])),
      'suggested_retail' => floatval(str_replace(',', '.', $line[37])),
    );
    $prices[$line[1]][] = $price;

    if(count($products) == 100){
      oekoring_insert_products_prices($products, $prices);
      $products = array();
      $prices = array();
    }
  }
  if(count($products)){
    oekoring_insert_products_prices($products, $prices);
  }
  SQL::update("UPDATE msl_products SET status='d' WHERE import_status='d' AND supplier_id='35'");
  if($full){
    SQL::update("UPDATE msl_products SET status='d' WHERE import_status='n' AND supplier_id='35'");
  }
  #update msl_prices set price=(purchase*((100+tax)/100))*1.3 where product_id in (select id from msl_products where supplier_id=35);
  #update msl_prices pr,msl_products p set pr.amount_per_bundle=p.amount_per_bundle where pr.product_id=p.id and p.supplier_id=35;
  #update msl_prices set price_bundle=(purchase*((100+tax)/100))*1.25 where product_id in (select id from msl_products where supplier_id=35);
  fclose($h);
  return 'ok '.print_r($header,1);
}

function oekoring_insert_products_prices($products, $prices){
  $qry = "INSERT INTO msl_products (supplier_id, supplier_product_id, name, type, status, import_status, amount_per_bundle, brand_id, gtin_piece, gtin_bundle) VALUES (".implode('),(', $products).") ON DUPLICATE KEY UPDATE name=VALUES(name), type=VALUES(type), import_status=VALUES(import_status), amount_per_bundle=VALUES(amount_per_bundle), brand_id=VALUES(brand_id), gtin_piece=VALUES(gtin_piece), gtin_bundle=VALUES(gtin_bundle), updated=NOW()";
  SQL::update($qry);
  $qry = "SELECT supplier_product_id, id FROM msl_products WHERE supplier_id='35' AND supplier_product_id IN (".SQL::escapeArray(array_keys($prices)).")";
  $pids = SQL::selectKey2Val($qry, 'supplier_product_id', 'id');
  $qry = "INSERT INTO msl_prices (product_id, start, end, purchase, purchase_promo, tax, purchase_bulk1_amount, purchase_bulk1, purchase_bulk2_amount, purchase_bulk2, suggested_retail) VALUES ";
  foreach($prices as $supplier_product_id => $pps){
    $product_id = $pids[$supplier_product_id];
    foreach($pps as $pp){
      array_unshift($pp, $product_id);
      $qry .= "(".SQL::escapeArray($pp)."),";
      #logger($product_id." ".$supplier_product_id." ".print_r($pp,1));
    }
  }
  $qry = rtrim($qry, ',')." ON DUPLICATE KEY UPDATE end=VALUES(end), purchase=VALUES(purchase), purchase_promo=VALUES(purchase_promo), tax=VALUES(tax), purchase_bulk1_amount=VALUES(purchase_bulk1_amount), purchase_bulk1=VALUES(purchase_bulk1), purchase_bulk2_amount=VALUES(purchase_bulk2_amount), purchase_bulk2=VALUES(purchase_bulk2), suggested_retail=VALUES(suggested_retail)";
  SQL::update($qry);
  #logger(print_r($pids,1));
}


#delete from msl_prices where start!='0000-00-00';
