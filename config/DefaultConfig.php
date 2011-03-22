<?php
class DefaultConfig
{
	public static $charset = 'utf-8';
	
	public static $debug = TRUE;
	
	public static $useMySQL = FALSE;
	public static $dbUser = '';
	public static $dbPass = '';
	public static $dbHost = '';
	public static $dbPort = 3306;
	public static $dbName = '';
	
	/* What additional libraries do you want Capsule to automatically load?
	 *   (by default the Library, Resource, Event, Template, OutputFormatter
	 *     are loaded)
	 */
	public static $extraLibraries = array(
		'tree' => 'lib/capsule/plugins/calltree/',
		'route' => 'lib/capsule/plugins/routing/'
	);

	public static $routes = array();
}