<?php
error_reporting(E_ERROR);
require __DIR__.'/../config/DefaultConfig.php';

$customConfigFile = __DIR__.'/../config/'.$_SERVER['HTTP_HOST'].'/UserConfig.php';
if( is_file($customConfigFile) ) {
	require $customConfigFile;
}
else {
	class UserConfig extends DefaultConfig{}
}

require __DIR__.'/../bootstrap.php';
