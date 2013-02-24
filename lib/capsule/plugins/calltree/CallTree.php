<?php
namespace org\capsule\plugins\calltree;
use org\capsule\Capsule as Capsule;
use org\capsule\event\CapsuleEvents as CapsuleEvents;
use org\capsule\event\EventManager as EventManager;

class CallTree
{
	protected $_callTree;
	protected $_currentNode;
	protected $_prevNode;
	protected $_currentDepth;

	protected $_capsule;
	
	public function __construct( Capsule &$capsule )
	{
		$this->_capsule =& $capsule;
		$this->_callTree = array();
		$this->_currentDepth = 0;
		
		$this->_capsule->event->addListener(
			CapsuleEvents::NewCall,
			array($this, 'newCall'),
			EventManager::HIGH
		);
		
		$this->_capsule->event->addListener(
			CapsuleEvents::FinishedCall,
			array($this, 'finishedCall')
		);
	}
	
	public function draw()
	{
		echo '<pre>'; print_r($this->getTree()); echo '</pre>';
	}

	public function currentDepth() {
		return $this->_currentDepth;
	}

	public function currentUrl() {
		return $this->_currentNode[0];
	}
	
	public function getTree()
	{
		return $this->_callTree;
	}
	
	public function newCall( $evt )
	{
		$url = $evt->info['url'];
		$this->_currentDepth++;
		
		if( $this->_currentNode === null ) {
			$this->_prevNode = null;
			$this->_currentNode = array($url);
			$this->_callTree[] =& $this->_currentNode;
		}
		else {
			$this->_prevNode =& $this->_currentNode;
			$newNode = array($url);
			$this->_currentNode[] =& $newNode;
			$this->_currentNode =& $newNode;
		}
	}
	
	public function finishedCall( $evt )
	{
		$this->_currentNode =& $this->_prevNode;
		$this->_currentDepth--;
	}
}