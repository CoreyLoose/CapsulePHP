<?php
namespace org\capsule\load;
use Exception as Exception;

class Model
{
	protected $_capsule;
	protected $_modelRoot;
	protected $_loaded = array();
	
	protected $_illegalModelNames = array('load');
	
	public function __construct( &$capsule, $modelRoot )
	{
		$this->_capsule =& $capsule;
		$this->_modelRoot = $modelRoot;
	}
	
	public function load( $modelName )
	{
		$origModelName = $modelName;
		$modelName = $modelName[0] . str_replace('_', '/', substr($modelName, 1));

		$slashPos = strrpos($modelName, '/');
		$dirName = '';
		if( $slashPos !== FALSE ) {
			$dirName = substr($modelName, 0, $slashPos + 1);
			$modelName = substr($modelName, $slashPos + 1);
		}
		
		if( $modelName[0] == '_' || in_array($modelName, $this->_illegalModelNames) ) {
			throw new Exception('Illegal model name "'.$modelName.'"');
		}
		
		$modelClassName = $modelName.'Model';
		$fileName = $this->_modelRoot.$dirName.$modelClassName.'.php';
		if( isset($this->_loaded[$fileName]) ) {
			return FALSE;
		}

		if( !$this->_capsule->library->isFileLoadable($fileName) ) {
			throw new Exception(
				'Unable to load "'.$fileName.'" using current include path'
			);	
		}

		$this->_loaded[$fileName] = true;
		require $fileName;
		$this->$origModelName = new $modelClassName();
	}

	public function __get($name) {
		if (!isset($this->$name)) {
			$this->load($name);
		}
		return $this->$name;
	}
}