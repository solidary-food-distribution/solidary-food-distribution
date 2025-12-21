<?php
declare(strict_types=1);

class Product{
  public int $id;
  public string $name;
  public string $supplier_name;
  public int $supplier_id;
  public string $type;
  public float $kg_per_piece;
  public float $amount_steps;
  public float $amount_min;
  public float $amount_max;
  public string $status;
  public string $stock;
  public int $amount_per_bundle;
  public string $supplier_product_id;
  public int $brand_id;
  public string $gtin_piece;
  public string $gtin_bundle;
  public string $infos;
  public string $category;

  public static function create($inserts){
    require_once('sql.inc.php');
    $fields = array_keys($inserts);
    array_walk($fields, 'sql_escape_fieldname');
    $qry="INSERT INTO msl_products (".implode(',', $fields).") VALUES (".sql_escape_array($inserts).")";
    $id = sql_insert($qry);
    if(!$id){
      return false;
    }
    $inserts['id'] = $id;
    $p = new Product();
    $r = new ReflectionClass($p);
    $props = $r->getProperties();
    foreach($props as $prop){
      #logger(print_r($prop,true));
      $type = $prop->getType();
      if($type == 'int'){
        $p->{$prop->name} = intval($inserts[$prop->name]);
      }elseif($type == 'string'){
        $p->{$prop->name} = strval($inserts[$prop->name]);
      }elseif($type == 'float'){
        $p->{$prop->name} = floatval($inserts[$prop->name]);
      }
    }
    #logger(print_r($p,true));
    return $p;
  }

  public function update( array $updates = array() ){
    require_once('sql.inc.php');
    $qry = 
      "UPDATE msl_products SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }

}
