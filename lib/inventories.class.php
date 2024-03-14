<?php
declare(strict_types=1);

require_once('inventory.class.php');

class Inventories extends ArrayObject{

  public static function create($delivery_item_id, $pickup_item_id, $product_id){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_inventory ".
        "(delivery_item_id, pickup_item_id, product_id) VALUES ".
        "('" . intval($delivery_item_id) . "', '" . intval($pickup_item_id) . "', '" . intval($product_id) . "')";
    $id = SQL::insert($qry);
    return $id;
  }

  public function __construct(array $filters=array(), array $orderby=array(), int $limit_start=0, int $limit_count=-1){
    $array = $this->load_from_db($filters, $orderby, $limit_start, $limit_count);
    parent::__construct($array);
  }

  public function first(){
    $array = $this->getArrayCopy();
    return $array[key($array)];
  }

  public function keys(){
    return array_keys($this->getArrayCopy());
  }

  private function load_from_db(array $filters, array $orderby, int $limit_start, int $limit_count){
    if(isset($filters['id'])){
      $filters['i.id'] = $filters['id'];
      unset($filters['id']);
    }
    require_once('sql.class.php');
    $qry=
      "SELECT ".
        "i.id AS i_id, i.delivery_item_id, i.pickup_item_id, i.product_id, i.amount_pieces, i.amount_weight, i.weight_min, i.weight_max, i.weight_avg, ".
        "p.pid AS p_id,p.name AS p_name, p.producer_id AS p_producer_id, mp.name AS p_producer_name, p.type AS p_type ".
      "FROM msl_inventory i ".
        "LEFT JOIN msl_products p ON (i.product_id=p.pid) ".
        "LEFT JOIN msl_members mp ON (p.producer_id=mp.id) ".
      "WHERE 1=1 ";
    if(!empty($filters)){
      $qry .= "AND ".SQL::buildFilterQuery($filters);
    }
    $qry .=
      "ORDER BY CASE WHEN p_type='p' THEN 0 WHEN p_type='k' THEN 1 ELSE 2 END,p_name";
    $dbrs=SQL::selectID($qry,'i_id');
    $array = array();
    foreach($dbrs as $i_id=>$i){
      $inventory = new Inventory();
      $inventory->id = intval($i['i_id']);
      $inventory->delivery_item_id = intval($i['delivery_item_id']);
      $inventory->pickup_item_id = intval($i['pickup_item_id']);
      $inventory->product = new Product();
      $inventory->product->id = intval($i['product_id']);
      $inventory->product->name = $i['p_name'];
      $inventory->product->producer = new Member();
      $inventory->product->producer->id = intval($i['p_producer_id']);
      $inventory->product->producer->name = $i['p_producer_name'];
      $inventory->product->type = $i['p_type'];
      $inventory->amount_pieces = intval($i['amount_pieces']);
      $inventory->amount_weight = floatval($i['amount_weight']);
      $inventory->weight_min = floatval($i['weight_min']);
      $inventory->weight_max = floatval($i['weight_max']);
      $inventory->weight_avg = floatval($i['weight_avg']);
      $array[$i_id] = $inventory;
    }
    return $array;
  }
}

function inventory_get($id){
  $objects = new Inventories(array('id' => $id));
  if(!empty($objects)){
    return $objects->first();
  }
  return null;
}