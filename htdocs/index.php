<?php
require realpath(__DIR__).'/../config/DefaultConfig.php';

$customConfigFile = realpath(__DIR__).'/../config/'.$_SERVER['HTTP_HOST'].'/UserConfig.php';
if( is_file($customConfigFile) ) {
	require $customConfigFile;
}
else {
	class UserConfig extends DefaultConfig{}
}

require realpath(__DIR__).'/../bootstrap.php';