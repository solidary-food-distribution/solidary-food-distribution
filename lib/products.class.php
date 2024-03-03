<?php
declare(strict_types=1);

require_once('product.class.php');

class Products extends ArrayObject{

  public static function create($producer_id){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_products ".
        "(producer_id, type, status) VALUES ".
        "('" . intval($producer_id) . "', 'v', 'o')";
    $product_id = SQL::insert($qry);
    return $product_id;
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
    if(isset($filters['product_id'])){
      $filters['p.pid'] = $filters['product_id'];
      unset($filters['product_id']);
    }
    require_once('sql.class.php');
    $qry = 
      "SELECT p.*, ".
        "pr.price, pr.tax, pr.tax_incl, pr.purchase, ".
        "mp.name AS mp_name ".
      "FROM msl_members mp, msl_products p LEFT JOIN msl_prices pr ON (pr.pid=p.pid AND pr.start<=CURDATE() AND pr.end>=CURDATE()) ".
      "WHERE mp.id=p.producer_id ".
        "AND p.type IN ('p','k','b','v') AND p.status IN ('o','d') ";
    if(!empty($filters)){
      $qry .= "AND ".SQL::buildFilterQuery($filters);
    }
    if(!empty($orderby)){
      $qry .= "ORDER BY ".SQL::buildOrderbyQuery($orderby);
    }else{
      $qry .= "ORDER BY status DESC,type,p.name";
    }
    #logger($qry);
    $res = SQL::selectID($qry, 'pid');

    $array = array();
    foreach($res as $pid => $row){
      $product = new Product();
      $product->id = intval($row['pid']);
      $product->name = $row['name'];
      $product->producer = new Member();
      $product->producer->id = intval($row['producer_id']);
      $product->producer->name = $row['mp_name'];
      $product->type = $row['type'];
      $product->period = $row['period'];
      $product->amount_steps = floatval($row['amount_steps']);
      $product->amount_min = floatval($row['amount_min']);
      $product->amount_max = floatval($row['amount_max']);
      $product->orders_lock_date = $row['orders_lock_date']; //REFACTOR new DateTime($row['orders_lock_date']);
      $product->price = floatval($row['price']);
      $product->tax = floatval($row['tax']);
      $product->tax_incl = boolval($row['tax_incl']);
      $product->purchase = floatval($row['purchase']);
      $array[$pid]=$product;
    }
    return $array;
  }

}