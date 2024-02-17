<?php
declare(strict_types=1);

require_once('pickup.class.php');

class Pickups extends ArrayObject{

  public static function create($member_id, $user_id){
    require_once('sql.class.php');
    $qry = 
      "INSERT INTO msl_pickups ".
        "(member_id, user_id, created) VALUES ".
        "('" . intval($member_id) . "', '" . intval($user_id) . "', NOW())";
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
      $filters['pu.id'] = $filters['id'];
      unset($filters['id']);
    }
    if(isset($filters['member_id'])){
      $filters['m.id'] = $filters['member_id'];
      unset($filters['member_id']);
    }
    require_once('sql.class.php');
    $qry=
      "SELECT pu.id AS pickup_id, pu.status AS pu_status, m.id AS member_id, m.name AS member_name, pu.created AS pu_created, u.id AS user_id, u.name AS user_name, ".
        "pui.id AS pui_id, pui.delivery_id, pui.product_id, pui.amount_pieces, pui.amount_weight, ".
        "pui.price_type, pui.price, pui.price_sum, pui.best_before, pui.preference_value, ".
        "p.pid AS p_id, p.name AS p_name, p.producer_id AS p_producer_id, mp.name AS p_producer_name, p.type AS p_type, ".
        "(SELECT amount FROM msl_orders o WHERE o.member_id=m.id AND o.pid=pui.product_id) AS amount_order ".
      "FROM msl_members m, msl_users u, msl_pickups pu ".
        "LEFT JOIN msl_pickup_items pui ON (pu.id = pui.pickup_id) ".
        "LEFT JOIN msl_products p ON (pui.product_id = p.pid) ".
        "LEFT JOIN msl_members mp ON (p.producer_id = mp.id) ".
      "WHERE pu.member_id = m.id AND pu.user_id = u.id ";
    if(!empty($filters)){
      $qry .= "AND ".SQL::buildFilterQuery($filters);
    }
    $qry .=
      "ORDER BY pu.id, (CASE WHEN p_type='v' THEN 2 WHEN p_type='k' THEN 1 ELSE 0 END), p_name, pui.id";
    $pus=SQL::selectID2($qry,'pickup_id','pui_id');
    $pickups = array();
    foreach($pus as $pu_id=>$pu){
      $data = $pu[key($pu)];
      $pickup = new Pickup();
      $pickup->id = $data['pickup_id'];
      $pickup->status = $data['pu_status'];
      $member = new Member();
      $member->id = intval($data['member_id']);
      $member->name = $data['member_name'];
      $pickup->member = $member;
      $pickup->created = new DateTime($data['pu_created']);
      $user = new User();
      $user->id = intval($data['user_id']);
      $user->name = $data['user_name'];
      $pickup->user = $user;
      foreach($pu AS $pui_id => $pui){
        if((string)$pui_id===''){
          continue;
        }
        $item = new PickupItem();
        $item->id = intval($pui_id);
        $item->product_id = intval($pui['p_id']);
        $item->amount_order = floatval($pui['amount_order']);
        $item->amount_pieces = floatval($pui['amount_pieces']);
        $item->amount_weight = floatval($pui['amount_weight']);
        $item->price_type = $pui['price_type'];
        $item->price = floatval($pui['price']);
        $item->price_sum = floatval($pui['price_sum']);
        if($pui['best_before']!=='0000-00-00'){
          $item->best_before = new DateTime($pui['best_before']);
        }
        $item->product = new Product();
        $item->product->id = $item->product_id;
        if($item->product_id){
          $item->product->name = $pui['p_name'];
          $item->product->producer = new Member();
          $item->product->producer->id = intval($pui['p_producer_id']);
          $item->product->producer->name = $pui['p_producer_name'];
          $item->product->type = $pui['p_type'];
        }else{
          $item->product->name = '[gelöschtes Produkt]';
          $item->product->producer = new Member();
          $item->product->producer->id = 0;
          $item->product->producer->name = '';
          $item->product->type = '';
        }
        $pickup->items[$pui_id] = $item;
      }
      $pickups[$pu_id] = $pickup;
    }
    return $pickups;
  }

}


function pickup_get($id, $member_id){
  $objects = new Pickups(array('id' => $id, 'member_id' => $member_id));
  if(!empty($objects)){
    return $objects->first();
  }
  return null;
}