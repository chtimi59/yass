<?php
date_default_timezone_set('America/Montreal');

define('MYSQL_TABLE_ASSETS', "assets");
define('MYSQL_TABLE_DISPLAY',"displays");

/* Asset Status */
define('STATUS_BACKSTAGE', 0);
define('STATUS_PENDING',   1);
define('STATUS_LIVE',      2);
define('STATUS_FINISHED',  3);
?>