<?php
use org\capsule\event\Event as Event;
use org\capsule\event\CapsuleEvents as CapsuleEvents;

//------------------------------------------------------------------
// PHP Cleanup - Could be moved to your webserver config
//------------------------------------------------------------------

set_include_path(__DIR__.':'.__DIR__.'/lib');
ini_set('magic_quotes_runtime', 0);


//------------------------------------------------------------------
// Get us a Capsule
//------------------------------------------------------------------

require_once 'lib/capsule/Capsule.php';

class CapsuleInstance
{
	public static $instance;
}

function capsule()
{ 
	return CapsuleInstance::$instance;
}

CapsuleInstance::$instance = new org\capsule\Capsule(array(
	'charset' => UserConfig::$charset,
	'debug' => UserConfig::$debug,
	'extraLibraries' => UserConfig::$extraLibraries
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