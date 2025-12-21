<?php

function oekoring_download_bnns(){
  $env = parse_ini_file('../config/oekoring.env');
  $ftp = ftp_connect($env['FTP_HOST'], 21);
  if(!$ftp){
    return array('error' => 'connect to FTP failed');
  }
  $login = @ftp_login($ftp, $env['FTP_USER'], $env['FTP_PWD']);
  if(!$login){
    return array('error' => 'login to FTP failed');
  }
  ftp_pasv($ftp, true);
  $files = ftp_nlist($ftp, '.');
  foreach($files as $file){
    //if(substr($file, -4, 4) == '.BNN'){
    if($file == 'PL.BNN'){
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
  require_once('sql.inc.php');
  if($full){
    sql_update("UPDATE msl_products SET import_status='n' WHERE supplier_id='35'");
  }

  $brands = sql_select_key2value("SELECT bnn,id FROM msl_brands WHERE supplier_id=35", 'bnn', 'id');
  $categories = sql_select_key2value("SELECT wg_nr,(CASE WHEN wg_ersatz>0 THEN (SELECT wg_name FROM msl_wg_oeko wg2 WHERE wg2.wg_nr=wg.wg_ersatz) ELSE wg_name END) wg_name FROM msl_wg_oeko wg", 'wg_nr' , 'wg_name');

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
    $category = '';
    if(isset($categories[$line[19]])){
      $category = $categories[$line[19]];
    }
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
      'n', //no stock
      $status, //import_status
      str_replace(',', '.', $line[23]), //amount_per_bundle
      $brand_id,
      $line[5], //gtin_piece
      $line[6], //gtin_bundle
      $category,
    );
    $products[$linenr]=sql_escape_array($row);
    $start = $prices_start;
    $end = $prices_end;
    $promo = 0;
    if($line[62] == 'A'){
      $start = substr($line[63],0,4).'-'.substr($line[63],4,2).'-'.substr($line[63],6,2);
      $end = substr($line[64],0,4).'-'.substr($line[64],4,2).'-'.substr($line[64],6,2);
      if($end == '0--'){
        $end = '9999-12-31';
      }
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
  sql_update("UPDATE msl_products SET status='d' WHERE import_status='d' AND supplier_id='35'");
  if($full){
    sql_update("UPDATE msl_products SET status='d' WHERE import_status='n' AND supplier_id='35'");
  }
  fclose($h);

  $category_replace = array(
    'Bananen' => 'Gemüse, Obst usw',
    'Cidre, Apfelmost' => 'Getränke',
    'Deko' => 'Non-Food',
    'Duftöle' => 'Non-Food',
    'Einmalartikel 7 %' => '',
    'Eintöpfe, Suppen, Fertiggerichte' => 'Fertiggerichte',
    'Essig' => 'Essig, Öl',
    'Fertiggerichte gekühlt' => 'Fertiggerichte',
    'Fertigsalate/Halbfertigprodukte' => 'Fertiggerichte',
    'Fruchtsäfte' => 'Getränke',
    'Fruchtschnitten' => 'Naschen',
    'frische Pasta' => 'Nudeln',
    'Gemüsesäfte' => 'Getränke',
    'Gemüsebrühe, Würzmittel' => 'Würzmittel',
    'Gewürze' => 'Würzmittel',
    'Getreide' => 'Getreideprodukte',
    'Honig' => 'Süßungsmittel',
    'Hülsenfrüchte' => 'Hülsenfrüchte, Saaten usw',
    'Hygieneartikel' => 'Non-Food',
    'Kaffee, Getreidekaffee,Kakao' => 'Kaffee, Tee usw',
    'Keimsaaten' => 'Hülsenfrüchte, Saaten usw',
    'Kekse, Süßigkeiten' => 'Naschen',
    'Knabbersachen pikant, Brote' => 'Naschen',
    'Tee' => 'Kaffee, Tee usw',
    'Konserven fruchtig' => 'Konserven',
    'Konserven pikant' => 'Konserven',
    'Kosmetik' => 'Non-Food',
    'Limonade' => 'Getränke',
    'Makrobiotik' => 'Senf, Mayo usw',
    'Meerrettich' => 'Senf, Mayo usw',
    'Mineralwasser' => 'Getränke',
    'Müsli etc.' => 'Müsli usw',
    'Nüsse, frisch' => 'Nüsse',
    'Nußmuse' => 'Aufstriche',
    'Obst' => 'Gemüse, Obst usw',
    'Ölsaaten' => 'Hülsenfrüchte, Saaten usw',
    'P f a n d' => '',
    'Pilze' => 'Gemüse, Obst usw',
    'Pudding, Desserts' => 'Naschen',
    'Pudding' => 'Naschen',
    'Reiswaffeln' => 'Naschen',
    'lavera' => 'Kosmetik',
    'Salz' => 'Würzmittel',
    'Seifen' => 'Non-Food',
    'Senf, Meerrettich,Mayon.,Dressing' => 'Senf, Mayo usw',
    'Speiseöle' => 'Essig, Öl',
    'Sojasaucen' => 'Würzmittel',
    'Waschmittel' => 'Non-Food',
    'Wein' => 'Alkohol',
    'Sekt, Champagner, Prosecco' => 'Alkohol',
    'Schnäpse, Liköre' => 'Alkohol',
    'Fruchtaufstriche' => 'Aufstriche',
    'pikante Brotaufstriche' => 'Aufstriche',
    'Verkaufshilfen' => '',
    'Wurzel und Knollengemüse' => 'Gemüse, Obst usw',
    'Texte/Zeitschriften' => '',

  );
  foreach($category_replace as $search => $replace){
    $qry = "UPDATE msl_products SET category='".sql_escape_string($replace)."' WHERE category='".sql_escape_string($search)."'";
    sql_update($qry);
  }

  $piece_factor = 0.7;
  $bundle_factor = 0.6;

  $qry = "UPDATE msl_prices pr, msl_products p SET pr.amount_per_bundle=p.amount_per_bundle WHERE p.id=pr.product_id AND p.supplier_id=35 AND start<=DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND end>=CURDATE()";
  sql_update($qry);
  $qry = "UPDATE msl_prices pr, msl_products p SET pr.price=ROUND((pr.suggested_retail-ROUND(pr.purchase + (pr.purchase*pr.tax/100),2))*".$piece_factor.",2)+ROUND(pr.purchase + (pr.purchase*pr.tax/100),2) WHERE p.id=pr.product_id AND p.supplier_id=35 AND pr.suggested_retail>0 AND pr.start<=DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND pr.end>=CURDATE()";
  sql_update($qry);
  $qry = "UPDATE msl_prices pr, msl_products p SET pr.price_bundle=ROUND((pr.suggested_retail-ROUND(pr.purchase + (pr.purchase*pr.tax/100),2))*".$bundle_factor.",2)+ROUND(pr.purchase + (pr.purchase*pr.tax/100),2) WHERE p.id=pr.product_id AND p.supplier_id=35 AND pr.amount_per_bundle>1 AND pr.suggested_retail>0 AND pr.start<=DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND pr.end>=CURDATE()";
  sql_update($qry);

  $qry = "UPDATE msl_prices pr, msl_products p SET p.status='n' WHERE p.id=pr.product_id AND p.supplier_id=35 AND pr.price=0 AND pr.price_bundle=0 AND pr.start<=DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND pr.end>=CURDATE()";
  sql_update($qry);

  $qry = "UPDATE msl_products SET status='n' WHERE supplier_id=35 AND category='Tiefkühlprodukte'";
  sql_update($qry);

  $qry = "UPDATE msl_products SET status='n' WHERE supplier_id=35 AND (BINARY name LIKE '% TK %' OR BINARY name LIKE '% TK' OR BINARY name LIKE '%TK %')";
  sql_update($qry);

  $qry = "UPDATE msl_products SET status='n' WHERE supplier_id=35 AND category=''";
  sql_update($qry);

  $qry = "UPDATE msl_products SET stock='o' WHERE supplier_id=35 AND status='o'";
  sql_update($qry);

  return 'ok '.print_r($header,1);
}

function oekoring_insert_products_prices($products, $prices){
  $qry = "INSERT INTO msl_products (supplier_id, supplier_product_id, name, type, status, stock, import_status, amount_per_bundle, brand_id, gtin_piece, gtin_bundle, category) VALUES (".implode('),(', $products).") ON DUPLICATE KEY UPDATE name=VALUES(name), type=VALUES(type), import_status=VALUES(import_status), amount_per_bundle=VALUES(amount_per_bundle), brand_id=VALUES(brand_id), gtin_piece=VALUES(gtin_piece), gtin_bundle=VALUES(gtin_bundle), category=VALUES(category), stock=VALUES(stock), updated=NOW()";
  sql_update($qry);
  $qry = "SELECT supplier_product_id, id FROM msl_products WHERE supplier_id='35' AND supplier_product_id IN (".sql_escape_array(array_keys($prices)).")";
  $pids = sql_select_key2value($qry, 'supplier_product_id', 'id');
  $qry = "INSERT INTO msl_prices (product_id, start, end, purchase, purchase_promo, tax, purchase_bulk1_amount, purchase_bulk1, purchase_bulk2_amount, purchase_bulk2, suggested_retail) VALUES ";
  foreach($prices as $supplier_product_id => $pps){
    $product_id = $pids[$supplier_product_id];
    foreach($pps as $pp){
      array_unshift($pp, $product_id);
      $qry .= "(".sql_escape_array($pp)."),";
      #logger($product_id." ".$supplier_product_id." ".print_r($pp,1));
    }
  }
  $qry = rtrim($qry, ',')." ON DUPLICATE KEY UPDATE end=VALUES(end), purchase=VALUES(purchase), purchase_promo=VALUES(purchase_promo), tax=VALUES(tax), purchase_bulk1_amount=VALUES(purchase_bulk1_amount), purchase_bulk1=VALUES(purchase_bulk1), purchase_bulk2_amount=VALUES(purchase_bulk2_amount), purchase_bulk2=VALUES(purchase_bulk2), suggested_retail=VALUES(suggested_retail)";
  sql_update($qry);
  #logger(print_r($pids,1));
}


#delete from msl_prices where start!='0000-00-00';
