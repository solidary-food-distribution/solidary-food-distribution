<?php
require_once('order_product.class.php');

class Order{
  private $products=array();

  public function __construct($member_id,$init_with_database=true){
    if($init_with_database){
      require_once('sql.class.php');
      $qry=QRY_ORDERPRODUCT;
      $qry=str_replace('/*MEMBER_ID*/',intval($member_id),$qry);
      $qry=str_replace('/*AND*/','',$qry);
      $res=SQL::selectID($qry,'pid');
      foreach($res as $pid=>$dbop){
        $op=new OrderProduct($pid,$dbop['member_id'],false);
        $op->init_with_array($dbop);
        $this->products[$pid]=$op;
      }
    }
  }

  public function get_products(){
    return $this->products;
  }


}
