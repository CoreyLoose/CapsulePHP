<?php
namespace org\capsule\content\template;
use Exception as Exception;

class Template
{
	protected $_capsule;
	
	public function __construct( &$capsule )
	{
		$this->_capsule =& $capsule;
	}
	
	public function draw( $view, $vars = array() )
	{
		$fileName = $this->_capsule->viewDir.$view.'.php';
		
		if( !$this->_capsule->library->isFileLoadable($fileName) ) {
			throw new Exception(
				'Unable to load view "'.$fileName.'" using current include path'
			);	
		}
		
		$viewFunc = function( $pathToView, $vars ) {
			foreach( $vars as $name => &$val ) {
				$$name = $val;
			}
			require $pathToView;
		};

		$viewFunc($fileName, $vars);
	}
	
	public function get( $view, $vars = array() )
	{
		ob_start();
		$this->draw($view, $vars);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}