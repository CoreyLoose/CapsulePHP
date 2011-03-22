<?php
namespace org\capsule\event;

class Event
{
	protected $_eventName;
	public $info;

	public function __construct( $name, &$info = array() )
	{
		$this->_eventName = $name;
		$this->info =& $info;
	}
	
	public function getName()
	{
		return $this->_eventName;
	}
}