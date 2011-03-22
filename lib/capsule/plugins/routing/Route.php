<?php
namespace org\capsule\plugins\routing;
use Exception as Exception;

class Route
{
	public $file;
	public $route;
	public $translatedRoute;
	protected $_urlVars;
	
	public function __construct( $route, $file )
	{
		$this->file = $file;
		$this->route = $route;
		$this->_urlVars = array();
		$this->translatedRoute = $this->_translateRoute($route);
	}
	
	public function match( $url )
	{
		$pregResult = preg_match_all($this->translatedRoute, $url, $matches);

		if( $pregResult < 1 ) {
			return FALSE;
		}
		
		array_shift($matches); //get rid of the full match
		$numMatches = count($matches);

		if( $numMatches != count($this->_urlVars) ) {
			throw new Exception(
				'Invalid routing. Nubmer of vars does not match with number '.
				 'of url segments'
			);
		}
		
		$assocMatches = array();
		for( $i = 0; $i < $numMatches; $i++ ) {
			$assocMatches[$this->_urlVars[$i]] = $matches[$i][0];
		}

		return $assocMatches;
	}
	
	protected function _translateRoute( $route )
	{
		$translatedRoute =
			'/^'.
			str_replace(
				'/',
				'\/',
				preg_replace_callback(
					'/:[^\/]+/',
					array($this, '_translateRouteCallback'),
					$route
				)				
			).
			'';
		
		//If the route has no trailing slash we add one
		//and make it optional, so we can match either case
		if( substr($translatedRoute, -1) != '/' ) {
			$translatedRoute .= '\/';
		}
		
		$translatedRoute .= '?$/';	
		
		return $translatedRoute;
	}
	
	protected function _translateRouteCallback( $matches )
	{
		$this->_urlVars[] = substr($matches[0],1);
		return '([^/]+)';
	}
}