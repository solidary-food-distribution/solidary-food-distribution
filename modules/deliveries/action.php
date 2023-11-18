<?php

require_once('inc.php');
user_ensure_authed();
user_needs_access('deliveries');

function execute_index(){
  require_once('sql.class.php');
  $qry=
    "SELECT d.id AS delivery_id, m.name AS producer_name, d.price_total AS d_price_total, d.created AS d_created, u.name AS creator_name, ".
      "COUNT(*) AS di_items, SUM(di.price_sum) AS di_price_total ".
    "FROM msl_members m, msl_users u, msl_deliveries d LEFT JOIN msl_delivery_items di ON (d.id=di.delivery_id) ".
    "WHERE d.producer_id=m.id AND d.creator_id=u.id ".
    "GROUP BY d.id, m.name, d.price_total, d.created, u.name ".
    "ORDER BY d.id DESC";
  $deliveries=SQL::select($qry);
  return array('deliveries'=>$deliveries);
}