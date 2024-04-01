<?php
declare(strict_types=1);

require_once('order.class.php');

class Orders extends ArrayObject{

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
      "SELECT p.*, o.member_id, o.amount, o.lock_date, ".
        "pr.price, pr.tax, pr.purchase, ".
        "mp.name AS mp_name, ".
        "mo.name AS mo_name, mo.identification AS mo_identification ".
      "FROM msl_members mp, msl_prices pr, msl_products p ".
      "  LEFT JOIN msl_orders o ON (p.pid=o.pid /*FILTER_MEMBER_ID*/) ".
      "  LEFT JOIN msl_members mo ON (o.member_id = mo.id) ".
      "WHERE mp.id=p.producer_id AND pr.pid=p.pid AND pr.start<=CURDATE() AND pr.end>=CURDATE() ".
      "  AND p.type IN ('p','k','b') AND p.status IN ('o','d') ";
    if(isset($filters['member_id'])){
      $member_ids=explode(',', $filters['member_id']);
      $qry = str_replace('/*FILTER_MEMBER_ID*/', " AND o.member_id IN (".SQL::escapeArray($member_ids).")", $qry);
      unset($filters['member_id']);
    }
    if(!empty($filters)){
      $qry .= "AND ".SQL::buildFilterQuery($filters);
    }
    if(!empty($orderby)){
      $qry .= "ORDER BY ".SQL::buildOrderbyQuery($orderby);
    }else{
      $qry .= "ORDER BY status DESC,period DESC,type,p.name";
    }
    $recset = SQL::select($qry);

    $array = array();
    foreach($recset as $row){
      $order = new Order();
      $order->product = new Product();
      $order->product->id = intval($row['pid']);
      $order->product->name = $row['name'];
      $order->product->producer = new Member();
      $order->product->producer->id = intval($row['producer_id']);
      $order->product->producer->name = $row['mp_name'];
      $order->product->type = $row['type'];
      $order->product->period = $row['period'];
      $order->product->amount_steps = floatval($row['amount_steps']);
      $order->product->amount_min = floatval($row['amount_min']);
      $order->product->amount_max = floatval($row['amount_max']);
      $order->product->orders_lock_date = $row['orders_lock_date']; //REFACTOR new DateTime($row['orders_lock_date']);
      $order->product->price = floatval($row['price']);
      $order->product->tax = floatval($row['tax']);
      $order->product->purchase = floatval($row['purchase']);
      $order->product->tax_incl = true; //deprecated
      if($row['member_id']){
        $order->member = new Member();
        $order->member->id = intval($row['member_id']);
        $order->member->name = $row['mo_name'];
        $order->member->identification = $row['mo_identification'];
      }elseif(isset($member_ids) && count($member_ids)==1){
        $order->member = new Member();
        $order->member->id = intval($member_ids[0]);
      }
      $order->amount = floatval($row['amount']);
      $order->lock_date = (string)$row['lock_date']; //REFACTOR new DateTime($row['lock_date']);
      $array[$row['pid'].'-'.$row['member_id']]=$order;
    }
    return $array;
  }

}