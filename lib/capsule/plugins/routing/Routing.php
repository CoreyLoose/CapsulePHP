<?php
namespace org\capsule\plugins\routing;
use org\capsule\Capsule as Capsule;
use org\capsule\event\CapsuleEvents as CapsuleEvents;

class Routing
{
	protected $_capsule;
	protected $_routes;
	
	public function __construct( Capsule &$capsule )
	{
		$this->_capsule =& $capsule;
		$this->_routes = array();
		
		$this->_capsule->event->addListener(
			CapsuleEvents::NewCall,
			array($this, 'newCall')
		);
	}
	
	public function addRoute( $route, $file )
	{
		$this->_routes[] = new Route($route, $file);
	}
	
	public function newCall( &$evt )
	{
		foreach( $this->_routes as &$route ) {
			$match = $route->match($evt->info['url']);
			if( $match === FALSE ) {
				continue;
			}
			$evt->info['url'] = $route->file;
			foreach( $match as $key => $value ) {
				$evt->info['get'][$key] = $value;
			}
			return;
		}
	}
}