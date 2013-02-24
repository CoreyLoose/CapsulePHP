<?php
class DefaultConfig
{
	public static $charset = 'utf-8';
	
	public static $debug = FALSE;

	public static $assets_version = 100;
	public static $assets_url_patterns = array('/js/', '/css/');

	public static $useMySQL = true;

	public static $dbUser = 'root';
	public static $dbPass = '';
	public static $dbHost = 'localhost';
	public static $dbPort = 3306;
	public static $dbName = '';

	public static $errorLog = 'logs/error.log';
	public static $infoLog =  'logs/info.log';

	/* What additional libraries do you want Capsule to automatically load?
	 *   (by default the Library, Resource, Event, Template, OutputFormatter
	 *     are loaded)
	 */
	public static $extraLibraries = array(
		'tree' => 'lib/capsule/plugins/calltree/',
		'route' => 'lib/capsule/plugins/routing/',
		'util' => 'app/util/',
		'vo' => 'app/model/vo/',
	);

	public static $routes = array();
}
