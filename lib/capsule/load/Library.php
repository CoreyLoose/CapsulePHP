<?php
namespace org\capsule\load;
use Exception as Exception;

class Library
{
	protected $_capsule;
	protected $_loadFileName;
	protected $_loaded;
	
	protected $_illegalLibraryNames = array(
		'call', 'library', 'resource', 'charset', 'debug',
		'webDir', 'modelDir', 'viewDir', 'controlDir', 'model'
	);
	
	public function __construct( &$capsule, $loadFileName = '__load.php' )
	{
		$this->_capsule =& $capsule;
		$this->_loadFileName = $loadFileName;
		$this->_loaded = array();
	}
	
	/**
	 * Using the current php include path, the loader looks for a file 
	 * named $this->_loadFileName in the directory given. The
	 * file loaded has the option to declare a variable named $lib
	 * and set it equal to a value. If that happens the $lib value
	 * is set as a public property $name on the $capsule object
	 * 
	 * If the library has already been loaded once nothing will be done
	 * 
	 * An associative array of $loaderVars may also be specified.
	 * If they are they are created as variables in the load files
	 * scope to assist in the loading process.
	 *   (The capsule object is automatically added)
	 *
	 * @param scalar $name
	 * @param string $pathToDir
	 * @param array $loaderVars
	 */
	public function load( $name, $pathToDir, $loaderVars = array() )
	{
		if( $name[0] == '_' || in_array($name, $this->_illegalLibraryNames) ) {
			throw new Exception('Illegal library name "'.$name.'"');
		}
		
		$fileName = $pathToDir.$this->_loadFileName;
		
		if( isset($this->_loaded[$fileName]) ) {
			return FALSE;
		}
		
		if( !$this->isFileLoadable($fileName) ) {
			throw new Exception(
				'Unable to load "'.$fileName.'" using current include path'
			);	
		}
		
		$loaderVars['capsule'] =& $this->_capsule;
		
		$loader = function($fileName, $loaderVars) {
			foreach( $loaderVars as $key => &$value ) {
				$$key =& $value;
			}
			require $fileName;
			if( isset($lib) ) {
				return $lib;	
			}
			return TRUE;
		};
		
		$this->_loaded[$fileName] = TRUE;
		
		$loadResponse = $loader($fileName, $loaderVars);
		
		if( $loadResponse === FALSE || $loadResponse === TRUE ) {
			return;
		}
		$this->_capsule->$name = $loadResponse;
	}
	
	public function isFileLoadable( $filePath )
	{
		$fileHandle = @fopen($filePath, 'r', TRUE);
		if( $fileHandle === FALSE ) {
			return FALSE;
		}
		fclose($fileHandle);
		return TRUE;
	}
}