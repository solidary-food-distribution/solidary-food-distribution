<?php

if(in_array('-x', $argv)){
  $ip = '';
}else{
  $cmd = "dig +short myip.opendns.com @resolver1.opendns.com";
  $out = array();
  exec($cmd, $out);
  $ip = $out[0];
}

$cmd = "curl -k -s https://buchen.mit-sinn-leben.de/remote/reverse_ssh_ip_set?ip=".$ip;
system($cmd);
echo "\n";
