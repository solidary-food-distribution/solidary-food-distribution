<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('orders');

function execute_index(){
  require_once('sql.class.php');
  $qry=
    "SELECT p.pid,p.name,p.type,p.period, ".
      "o.amount,pr.price,pr.tax,pr.purchase, ".
      "m.id AS member_id,m.name AS m_name, ".
      "(SELECT mp.name FROM msl_members mp WHERE mp.id=p.producer_id) AS producer_name ".
    "FROM msl_orders o, msl_products p, msl_prices pr, msl_members m ".
    "WHERE o.pid=p.pid AND o.member_id=m.id AND o.amount>0 ".
      "AND p.pid=pr.pid AND pr.start<=CURDATE() AND pr.end>=CURDATE() ".
    "ORDER BY p.period DESC,p.type,p.name,m.name";
  $res=SQL::select($qry);
  $orders=array();
  foreach($res as $v){
    $orders[$v['pid']]['name']=$v['name'];
    $orders[$v['pid']]['type']=$v['type'];
    $orders[$v['pid']]['period']=$v['period'];
    $orders[$v['pid']]['price']=$v['price'];
    $orders[$v['pid']]['tax']=$v['tax'];
    $orders[$v['pid']]['purchase']=$v['purchase'];
    $orders[$v['pid']]['producer_name']=$v['producer_name'];
    if(!isset($orders[$v['pid']]['amount'])){
      $orders[$v['pid']]['amount']=0;
    }
    $orders[$v['pid']]['amount']=$orders[$v['pid']]['amount']+$v['amount'];
    $orders[$v['pid']]['members'][$v['member_id']]['name']=$v['m_name'];
    $orders[$v['pid']]['members'][$v['member_id']]['amount']=$v['amount'];
  }
  return array('orders'=>$orders);

}
