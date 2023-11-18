<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('debits');

function execute_index(){
  require_once('sql.class.php');
  $qry=
    "SELECT m.id AS member_id,m.name,m.identification, ".
      "p.pid,p.name AS p_name,p.type AS p_type,p.period AS p_period, ".
      "pr.price,pr.tax,pr.tax_incl,".
      "o.amount ".
    "FROM msl_members m, msl_orders o, msl_products p, msl_prices pr ".
    "WHERE m.consumer=1 AND m.id=o.member_id AND o.pid=p.pid ".
      "AND p.pid=pr.pid AND pr.start<=CURDATE() AND pr.end>=CURDATE() ".
      "AND o.amount>0 ".
    "ORDER BY m.name,p.period DESC,p.type,p.name";
  $res=SQL::select($qry);
  $members=array();
  foreach($res as $v){
    $members[$v['member_id']]['name']=$v['name'];
    $members[$v['member_id']]['identification']=$v['identification'];
    $members[$v['member_id']]['products'][$v['pid']]['name']=$v['p_name'];
    $members[$v['member_id']]['products'][$v['pid']]['type']=$v['p_type'];
    $members[$v['member_id']]['products'][$v['pid']]['period']=$v['p_period'];
    $members[$v['member_id']]['products'][$v['pid']]['amount']=$v['amount'];
    $members[$v['member_id']]['products'][$v['pid']]['price']=$v['price'];
    $members[$v['member_id']]['products'][$v['pid']]['tax']=$v['tax'];
    $members[$v['member_id']]['products'][$v['pid']]['tax_incl']=$v['tax_incl'];
  }
  foreach($members as $member_id=>$member){
    $members[$member_id]['products'][0]=array(
      'name'=>'Grundbeitrag',
      'type'=>'p',
      'period'=>'m',
      'amount'=>1,
      'price'=>28,
      'tax'=>7,
      'tax_incl'=>1
    );
  }
  return array('members'=>$members);

}