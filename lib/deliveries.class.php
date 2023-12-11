<?php
declare(strict_types=1);

require_once('delivery.class.php');

class Deliveries extends ArrayObject{

  public static function create($supplier_id, $creator_id){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_deliveries ".
        "(supplier_id, creator_id, created) VALUES ".
        "('" . intval($supplier_id) . "', '" . intval($creator_id) . "', NOW())";
    $delivery_id = SQL::insert($qry);
    return $delivery_id;
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
      $filters['d.id'] = $filters['id'];
      unset($filters['id']);
    }
    require_once('sql.class.php');
    $qry=
      "SELECT d.id AS delivery_id, ms.id AS supplier_id, ms.name AS supplier_name, d.price_total AS d_price_total, d.created AS d_created, u.id AS creator_id, u.name AS creator_name, ".
        "di.id AS di_id, di.product_id, di.amount_pieces, di.amount_weight, di.price_type, di.price, di.price_sum, di.dividable, di.best_before, di.weight_min, di.weight_max, di.weight_avg, ".
        "p.pid AS p_id,p.name AS p_name, p.producer_id AS p_producer_id, mp.name AS p_producer_name, p.type AS p_type ".
      "FROM msl_members ms, msl_users u, msl_deliveries d ".
        "LEFT JOIN msl_delivery_items di ON (d.id=di.delivery_id) ".
        "LEFT JOIN msl_products p ON (di.product_id=p.pid) ".
        "LEFT JOIN msl_members mp ON (p.producer_id=mp.id) ".
      "WHERE d.supplier_id=ms.id AND d.creator_id=u.id ";
    if(!empty($filters)){
      $qry .= "AND ".SQL::buildFilterQuery($filters);
    }
    $qry .=
      "ORDER BY d.id,di.id";
    $ds=SQL::selectID2($qry,'delivery_id','di_id');
    $deliveries = array();
    foreach($ds as $d_id=>$d){
      $data=$d[key($d)];
      $delivery=new Delivery();
      $delivery->id=$data['delivery_id'];
      $supplier=new Member();
      $supplier->id=intval($data['supplier_id']);
      $supplier->name=$data['supplier_name'];
      $delivery->supplier=$supplier;
      $delivery->price_total=floatval($data['d_price_total']);
      $delivery->created=new DateTime($data['d_created']);
      $creator=new User();
      $creator->id=intval($data['creator_id']);
      $creator->name=$data['creator_name'];
      $delivery->creator=$creator;
      foreach($d AS $di_id=>$di){
        if((string)$di_id===''){
          continue;
        }
        $item = new DeliveryItem();
        $item->id = intval($di_id);
        $item->product_id = intval($di['p_id']);
        $item->amount_pieces = intval($di['amount_pieces']);
        $item->amount_weight = floatval($di['amount_weight']);
        $item->price_type = $di['price_type'];
        $item->price = floatval($di['price']);
        $item->price_sum = floatval($di['price_sum']);
        $item->dividable = floatval($di['dividable']);
        if($di['best_before']!=='0000-00-00'){
          $item->best_before = new DateTime($di['best_before']);
        }
        $item->weight_min = floatval($di['weight_min']);
        $item->weight_max = floatval($di['weight_max']);
        $item->weight_avg = floatval($di['weight_avg']);
        $item->product = new Product();
        $item->product->id = $item->product_id;
        if($item->product_id){
          $item->product->name = $di['p_name'];
          $item->product->producer = new Member();
          $item->product->producer->id = intval($di['p_producer_id']);
          $item->product->producer->name = $di['p_producer_name'];
          $item->product->type = $di['p_type'];
        }else{
          $item->product->name = '[gelöschtes Produkt]';
          $item->product->producer = new Member();
          $item->product->producer->id = 0;
          $item->product->producer->name = '';
          $item->product->type = '';
        }
        $delivery->items[$di_id]=$item;
      }
      $deliveries[$d_id]=$delivery;
    }
    return $deliveries;
  }

}
