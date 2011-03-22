<?php
namespace org\capsule\load;
use Exception as Exception;

class Resource
{
	protected $_webRoot;
	
	protected $_js = array();
	protected $_css = array();
	
	public function __construct( $webRoot )
	{
		$this->_webRoot = $webRoot;
	}
	
	public function getJs()
	{
		return array_keys($this->_js);
	}
	
	public function getCss()
	{
		return array_keys($this->_css);
	}
	
	public function load( $webPathToResource )
	{
		$lastDot = strrpos($webPathToResource, '.');
		if( $lastDot === FALSE ) {
			throw new Exception(
				'Loaded resource "'.$webPathToResource.'" '.
				'must have a file extension'
			);
		}
		
		$fileExt = strtoupper(substr($webPathToResource,$lastDot+1));
		
		switch( $fileExt )
		{
			case 'JS':
				$this->_js[$webPathToResource] = TRUE;
				break;
			case 'CSS':
				$this->_css[$webPathToResource] = TRUE;
				break;
			default:
				throw new Exception(
					'Loaded resource "'.$webPathToResource.'" '.
					'has an invalid file extension'
				);
		}
	}
}