<?php
use org\capsule\event\Event as Event;
use org\capsule\event\CapsuleEvents as CapsuleEvents;

//------------------------------------------------------------------
// PHP Cleanup - Could be moved to your webserver config
//------------------------------------------------------------------

set_include_path(__DIR__.':'.__DIR__.'/lib');
ini_set('magic_quotes_runtime', 0);

//------------------------------------------------------------------
// CLI Setup
//------------------------------------------------------------------
if (defined('STDIN')) {
	define('CLI', 1);
	require_once __DIR__.'/config/DefaultConfig.php';
	if (isset($argv)) {
		if (isset($argv[1]) && $argv[1] != '^') {
			$_SERVER['REQUEST_URI'] = $argv[1];
		}
		$config_to_use = 'cli';
		if (isset($argv[2])) {
			$config_to_use = $argv[2];
		}
		require_once __DIR__.'/config/'.$config_to_use.'/UserConfig.php';
	}
} else {
	define('CLI', 0);
}
//------------------------------------------------------------------
// Session setup
//------------------------------------------------------------------
if (!CLI) {
	session_set_cookie_params(60*60*24*7);
	session_start();
}

//------------------------------------------------------------------
// Logging setup
//------------------------------------------------------------------

if (UserConfig::$errorLog[0] != '/') {
	UserConfig::$errorLog = __DIR__.'/'.UserConfig::$errorLog;
}
if (UserConfig::$infoLog[0] != '/') {
	UserConfig::$infoLog = __DIR__.'/'.UserConfig::$infoLog;
}
ini_set('error_log',UserConfig::$errorLog);

//------------------------------------------------------------------
// Get us a Capsule
//------------------------------------------------------------------

define('CODE_ROOT', __DIR__);
require_once 'lib/capsule/Capsule.php';

class CapsuleInstance
{
	public static $instance;
}

/**
 * @return org\capsule\Capsule
 */
function capsule()
{ 
	return CapsuleInstance::$instance;
}

CapsuleInstance::$instance = new org\capsule\Capsule(array(
	'charset' => UserConfig::$charset,
	'debug' => UserConfig::$debug,
	'extraLibraries' => UserConfig::$extraLibraries
));

capsule()->library->load('logger', 'capsule/plugins/logger/', array(
	'error_log' => DefaultConfig::$errorLog,
	'info_log' => DefaultConfig::$infoLog
));

//------------------------------------------------------------------
// Pull routes from the config and give them to Capsule
//------------------------------------------------------------------

if( isset(capsule()->route) ) {
	foreach( UserConfig::$routes as $route => $file ) {
		capsule()->route->addRoute($route, $file);
	}
}

//------------------------------------------------------------------
// Create MySQL connection if enabled
//------------------------------------------------------------------

if( UserConfig::$useMySQL ) {
	capsule()->library->load('mysql', 'capsule/plugins/mysql/', array(
		'username' => UserConfig::$dbUser,
		'password' => UserConfig::$dbPass,
		'db' => UserConfig::$dbName,
		'host' => UserConfig::$dbHost,
		'port' => UserConfig::$dbPort,
	));
}

//------------------------------------------------------------------
// Make a Capsule request based off the browser/cli request
//------------------------------------------------------------------

capsule()->event->dispatch(new Event(CapsuleEvents::Ready));

if (isset($_SERVER['REQUEST_URI'])) {
	$output = capsule()->call(
		$_SERVER['REQUEST_URI'],
		array(
			'get' => $_GET,
			'post' => $_POST
		)
	);

	$eventInfo = array('output' => &$output);
	capsule()->event->dispatch(
		new Event(CapsuleEvents::Finished, $eventInfo)
	);

	echo $output;
}
