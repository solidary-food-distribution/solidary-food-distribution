<?php 

$fp = fopen('../../log/php_error.log', 'r');
fseek($fp, -5000, SEEK_END);
echo '<pre>';
echo fread($fp, 5000);
