<?php

require_once('inc.php');
user_ensure_authed();
user_has_access('admin');


function execute_index(){
  $products=user_has_access('products');
  $members=user_has_access('members');
  $users=user_has_access('users');
  $orders=user_has_access('orders');
  $debits=user_has_access('debits');
  if(!$products && !$members && !$users && !$orders && !$debits){
    forward_to_noaccess();
  }
  return array(
    'products'=>$products,
    'members'=>$members,
    'users'=>$users,
    'orders'=>$orders,
    'debits'=>$debits);
}
