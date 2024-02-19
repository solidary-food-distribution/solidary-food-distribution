<?php

require_once('inc.php');
require_once('sql.class.php');

function execute_index(){
  user_ensure_authed();
  user_needs_access('remote');
  $qry = "SELECT * FROM msl_var WHERE var IN ('remote_reverse_ssh_ip', 'remote_reverse_ssh_ip_read')";
  $res = SQL::selectID($qry, 'var');
  return array('var' => $res);
}

function execute_reverse_ssh_ip_set(){
  $ip = get_request_param('ip');
  $ip = preg_replace('/[^0-9\.]/', '', $ip);
  $qry = "INSERT INTO msl_var (var, value, updated) VALUES ('remote_reverse_ssh_ip', '".SQL::escapeString($ip)."', NOW()) ON DUPLICATE KEY UPDATE value = VALUES(value), updated = VALUES(updated)";
  SQL::update($qry);
  echo "$ip set";
  exit;
}

function execute_reverse_ssh_ip_get(){
  $qry = "SELECT value FROM msl_var WHERE var='remote_reverse_ssh_ip'";
  $res = SQL::selectOne($qry);
  $qry = "INSERT INTO msl_var (var, value, updated) VALUES ('remote_reverse_ssh_ip_read', NOW(), NOW()) ON DUPLICATE KEY UPDATE value = VALUES(value), updated = VALUES(updated)";
  SQL::update($qry);
  echo $res['value'];
  exit;
}