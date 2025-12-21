<?php


#echo product_import_csv('../data/products/20241129_Carmen.csv', 20, '2024-11-29', '2024-12-08');


function product_import_csv($file, $supplier_id, $prices_start, $prices_end){
  $h = fopen($file, 'r');
  $header = fgetcsv($h, 9999, ';');

  $full = true;

  require_once('sql.inc.php');
  if($full){
    sql_update("UPDATE msl_products SET import_status='n' WHERE supplier_id='".intval($supplier_id)."'");
  }


  $products = array();
  $prices = array();
  $linenr = 0;
  while($line = fgetcsv($h, 9999, ';')){
    $linenr++;
    $data = array_combine($header, $line);
    #print_r($data);
    $product = array();
    $product['name'] = trim($data['Artikel']);
    $product['supplier_id'] = $supplier_id;
    $product['supplier_product_id'] = $product['name'];
    if(!empty($data['Stück']) && $data['Einheit'] == 'kg'){
      $product['type'] = 'w';
    }elseif($data['Einheit'] == 'kg'){
      $product['type'] = 'k';
    }else{
      return 'Unbekannte Einheit '.print_r($data,1);
    }
    $product['kg_per_piece'] = 0;
    if($product['type'] == 'w'){
      $product['kg_per_piece'] = floatval(str_replace(',', '.', $data['Größe']));
    }
    $fields = array_keys($product);
    $products[$linenr] = sql_escape_array($product);

    $tax = 7;

    $price = array(
      'start' => $prices_start,
      'end' => $prices_end,
      'price' => floatval(str_replace(',', '.', $data['Preis final'])),
      'purchase' => floatval(trim(str_replace('€', '', str_replace(',', '.', $data['€/Einheit'])))), //price excl tax
      'tax' => $tax,
      'suggested_retail' => floatval(str_replace(',', '.', $data['UVP'])),
    );
    $prices[$product['supplier_product_id']] = $price;
  }
  fclose($h);

  $qry = "INSERT INTO msl_products (".implode(',', $fields).") VALUES (".implode('),(', $products).") ON DUPLICATE KEY UPDATE ";
  foreach($fields as $field){
    $qry .= $field."=VALUES(".$field."),";
  }
  $qry .= "updated=NOW()";
  sql_update($qry);

  $qry = "SELECT supplier_product_id, id FROM msl_products WHERE supplier_id='".intval($supplier_id)."' AND supplier_product_id IN (".sql_escape_array(array_keys($prices)).")";
  $pids = sql_select_key2value($qry, 'supplier_product_id', 'id');
  print_r($pids);
  
  $fields = array_keys($prices[key($prices)]);

  $qry = "INSERT INTO msl_prices (product_id, ".implode(',', $fields).") VALUES ";
  foreach($prices as $supplier_product_id => $pp){
    $product_id = $pids[$supplier_product_id];
    array_unshift($pp, $product_id);
    $qry .= "(".sql_escape_array($pp)."),";
    #logger($product_id." ".$supplier_product_id." ".print_r($pp,1));
  }
  $qry = rtrim($qry, ',')." ON DUPLICATE KEY UPDATE ";
  foreach($fields as $field){
    $qry .= $field."=VALUES(".$field."),";
  }
  $qry .= "updated=NOW()";
  sql_update($qry);

  sql_update("UPDATE msl_products SET status='o' WHERE supplier_id='".intval($supplier_id)."' AND supplier_product_id IN (".sql_escape_array(array_keys($prices)).")");

  return 'ok';
}

