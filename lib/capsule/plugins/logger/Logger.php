<?php
namespace org\capsule\plugins\logger;
use org\capsule\Capsule as Capsule;

class Logger
{
	protected $_capsule;

	protected $error_types;
	
	public function __construct( Capsule &$capsule, $error_log, $info_log )
	{
		$this->_capsule =& $capsule;
		$this->error_types = array(
			'error' => $error_log,
			'info' => $info_log
		);
	}

	public function log( $type, $message ) {
		if (!isset($this->error_types[$type])) {
			throw new Exception("No such error type: '$type'");
		}
		$message = $_SERVER['REQUEST_TIME'] .': '. trim(preg_replace("/\s+/", ' ', $message));
		error_log($message."\n", 3, $this->error_types[$type]);
 	}
}