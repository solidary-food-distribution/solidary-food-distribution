<?php

$env_file = __DIR__.'/../config/database.local.env';
$env = parse_ini_file($env_file);

$mysql_init_file = __DIR__.'/../sql/init.sql';

$cmd = "mysqldump --no-create-db --no-data --skip-comments --user=".escapeshellarg($env['DB_USER'])." --password=".escapeshellarg($env['DB_PWD'])." ".escapeshellarg($env['DB_DATABASE'])." | sed 's/ AUTO_INCREMENT=[0-9]*\b//g' > ".escapeshellarg($mysql_init_file);
system($cmd);