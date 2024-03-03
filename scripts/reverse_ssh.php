<?php

$out = array();
$cmd = "curl -k -s https://buchen.mit-sinn-leben.de/remote/reverse_ssh_ip_get";
exec($cmd, $out);
$ip = $out[0];

echo "$ip\n";