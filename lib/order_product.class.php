<?php
require_once('product.class.php');

define('QRY_ORDERPRODUCT',
  "SELECT p.*,o.*,pr.price,pr.tax,pr.tax_incl, ".
  "  (SELECT pu.name FROM msl_users pu WHERE pu.member_id=p.producer_id) AS producer_name ".
  "FROM msl_prices pr,msl_products p ".
  "  LEFT JOIN msl_orders o ON (p.pid=o.pid AND o.member_id='/*MEMBER_ID*/') ".
  "WHERE pr.pid=p.pid AND pr.start<=CURDATE() AND pr.end>=CURDATE() ".
  "  AND p.type IN ('p','k','b') AND p.status IN ('o','d') ".
  " /*AND*/ ".
  "ORDER BY status DESC,period DESC,type,name");

class OrderProduct extends Product{
  private $member_id;
  public $amount;
  public $lock_date;

  public function __construct($product_id,$member_id,$init_by_database=true){
    parent::__construct($product_id,false);
    $this->member_id=$member_id;
    if($init_by_database){
      require_once('sql.class.php');
      $qry=QRY_ORDERPRODUCT;
      $qry=str_replace('/*MEMBER_ID*/',intval($member_id),$qry);
      $qry=str_replace('/*AND*/'," AND p.pid='".intval($product_id)."'",$qry);
      $dbop=SQL::selectOne($qry);
      $this->init_with_array($dbop);
    }
  }

  public function init_with_array($array){
    parent::init_with_array($array);
    $this->amount=$array['amount'];
    $this->lock_date=$array['lock_date'];
  }

  public function is_locked(){
    if($this->orders_lock_date=='0000-00-00 00:00:00' || $this->orders_lock_date>date('Y-m-d H:i:s')){
      return false;
    }elseif($this->lock_date=='0000-00-00 00:00:00' || $this->lock_date>date('Y-m-d H:i:s')){
      return false;
    }
    return true;
  }

  public function change_amount($dir,$old_amount=null){
    if($old_amount!==null && floatval($old_amount)!=floatval($this->amount)){
      return 'old amount '.$old_amount.' does not match database amount '.$this->amount;
    }
    $requested_amount=null;
    if($dir=='-' && floatval($this->amount)==floatval($this->amount_min)){
      $requested_amount=0;
    }elseif($dir=='-'){
      $requested_amount=$this->amount - $this->amount_steps;
    }elseif($dir=='+' && floatval($this->amount)==0){
      $requested_amount=$this->amount_min;
    }elseif($dir=='+'){
      $requested_amount=$this->amount + $this->amount_steps;
    }
    if($this->is_locked()){
      return 'product is locked';
    }
    if($requested_amount!==null && ($requested_amount>=$this->amount_min || $requested_amount==0) && $requested_amount<=$this->amount_max){
      require_once('sql.class.php');
      $this->amount=$requested_amount;
      $qry="INSERT msl_orders (member_id,pid,amount) VALUES ('".intval($this->member_id)."','".intval($this->id)."','".floatval($this->amount)."') ON DUPLICATE KEY UPDATE amount=VALUES(amount)";
      SQL::update($qry);
    }
    return '';
  }

  public function get_amount_formated(){
    if($this->type=='b'){
      $amount=str_replace(',00','',str_replace('.',',',$this->amount));
    }else{
      $amount=str_replace('.',',',rtrim(rtrim($this->amount,'0'),'.'));
    }
    return $amount;
  }

}
