<?php
declare(strict_types=1);

require_once('product.class.php');
require_once('member.class.php');

class Order{
  public Product $product;
  public Member $member;
  public float $amount;
  public DateTime $lock_date;

  public function is_locked(){
    if($this->product->orders_lock_date=='0000-00-00 00:00:00' || $this->product->orders_lock_date>date('Y-m-d H:i:s')){
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
    if($dir=='-' && floatval($this->amount)==floatval($this->product->amount_min)){
      $requested_amount=0;
    }elseif($dir=='-'){
      $requested_amount=$this->amount - $this->product->amount_steps;
    }elseif($dir=='+' && floatval($this->amount)==0){
      $requested_amount=$this->product->amount_min;
    }elseif($dir=='+'){
      $requested_amount=$this->amount + $this->product->amount_steps;
    }
    if($this->is_locked()){
      return 'product is locked';
    }
    if($requested_amount!==null && ($requested_amount>=$this->product->amount_min || $requested_amount==0) && $requested_amount<=$this->product->amount_max){
      require_once('sql.class.php');
      $this->amount=$requested_amount;
      $qry="INSERT msl_orders (member_id,pid,amount) VALUES ('".intval($this->member->id)."','".intval($this->product->id)."','".floatval($this->amount)."') ON DUPLICATE KEY UPDATE amount=VALUES(amount)";
      SQL::update($qry);
    }
    return '';
  }

  public function get_amount_formated(){
    if($this->product->type=='b'){
      $amount=str_replace(',00','',str_replace('.',',',(string)$this->amount));
    }else{
      $amount=str_replace('.',',',(string)$this->amount);
    }
    return $amount;
  }
}
