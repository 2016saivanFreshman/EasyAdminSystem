<?php
//DIRECTORY_SEPARATOR是分隔符，用于linux和windows不同情况。
//str_replace用于替换（替换目标，替换的新品，被替换者）
define("WEB_PATH", str_replace(DIRECTORY_SEPARATOR, '/',str_replace('conf', '', dirname(__FILE__))));
define("WEB_URL", 'http://'.$_SERVER['HTTP_HOST']."/");
define("WEB_API", 'http://api'.str_replace('www', '', $_SERVER['HTTP_HOST']));

?>