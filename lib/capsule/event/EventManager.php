<?php
namespace org\capsule\event;
use org\capsule\event\Event as Event;
use Closure as Closure;
use Exception as Exception;

class EventManager
{
	const HIGH = 'HIGH';
	const MEDIUM = 'MEDIUM';
	const LOW = 'LOW';
	protected $_priorities = array(self::HIGH, self::MEDIUM, self::LOW);
	
	public $listeners = array();
	
	/**
	 * Prepares the given callback to be executed when the named 
	 * event is dispatched
	 * 
	 * The callback can either be an anonymous function or an old
	 * fashion php callback array. Most usefully would be to pass it
	 * in the form array($this, 'functionName') from another object
	 *
	 * If a priority is specified, it will control the order that listeners
	 * are notified about an event. The higher, the sooner.
	 * 
	 * @param scalar $eventName
	 * @param Closure|array $callback
	 * @param string $priority
	 */
	public function addListener(
		$eventName,
		$callback,
		$priority = self::MEDIUM
	) {
		if( !($callback instanceof Closure) && !is_array($callback) ) {
			Throw new Exception('Invalid callback');
		}
		if( !in_array($priority, $this->_priorities) ) {
			Throw new Exception('Invalid priority "'.$priority.'"');
		}
		
		if( !isset($this->listeners[$eventName]) ) {
			$this->listeners[$eventName] = array();
			foreach( $this->_priorities as $possiblePriority ) {
				$this->listeners[$eventName][$possiblePriority] = array();
			}
		}
		$this->listeners[$eventName][$priority][] = $callback;
	}
	
	/**
	 * Takes the events name and calls all listeners
	 * 
	 * @param Event $evt
	 */
	public function dispatch( Event &$evt )
	{
		if( !isset($this->listeners[$evt->getName()]) ) {
			return;
		}
		$listeners =& $this->listeners[$evt->getName()];
		foreach( $this->_priorities as $possiblePriority ) {
			foreach( $listeners[$possiblePriority] as &$listener ) {
				if( $listener instanceof Closure ) {
					$listener(&$evt);	
				}
				else {
					call_user_func($listener, &$evt);	
				}
			}
		}
	}
}