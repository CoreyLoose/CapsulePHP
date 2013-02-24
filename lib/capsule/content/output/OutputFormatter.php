<?php
namespace org\capsule\content\output;
use org\capsule\Capsule as Capsule;
use org\capsule\event\Event as Event;
use org\capsule\event\CapsuleEvents as CapsuleEvents;
use UserConfig as UserConfig;

class OutputFormatter
{
	protected $_capsule;
	
	const HTML = 'text/html';
	const XML = 'text/xml';
	const JSON = 'application/json';
	protected $_topLevelContent;
	
	protected $_currentTitle;
	protected $_currentDepth;
	
	public function __construct( &$capsule )
	{
		$this->_capsule =& $capsule;
		$this->_currentTitle = '';
		$this->_topLevelContent = self::HTML;
		$this->_currentDepth = 0;
		
		$this->_capsule->event->addListener(
			CapsuleEvents::NewCall,
			array($this, 'newCall')
		);
		
		$this->_capsule->event->addListener(
			CapsuleEvents::FinishedCall,
			array($this, 'finishedCall')
		);
		
		$this->_capsule->event->addListener(
			CapsuleEvents::Finished,
			array($this, 'finished')
		);
	}
	
	/**
	 * Set the current content type. This allows you to control what type of
	 * header will be used to display the current page execution.
	 *
	 * The setType call will be ignored by any nested calls so that preference
	 * is given to the originating call.
	 *
	 * If this function is never used the default header used is html
	 *
	 * @param scalar $type - allowed vals are constants on this class
	 * @return boolean
	 */
	public function setType( $type )
	{
		if( $this->_currentDepth > 1 ) {
			return FALSE;
		}
		$this->_topLevelContent = $type;
		return TRUE;
	}
	
	/**
	 * Set the current page title. This value will only be used if the content
	 * type is html
	 *
	 * The setType call will be ignored by any nested calls so that preference
	 * is given to the originating call.
	 *
	 * @param scalar $title
	 * @return boolean
	 */
	public function setTitle( $title )
	{
		if( $this->_currentDepth > 1 ) {
			return FALSE;
		}
		$this->_currentTitle = $title;
		return TRUE;
	}

	/**
	 * @param $title
	 * @return string
	 */
	public function getTitle( $title ) {
		return $this->_currentTitle;
	}

	/**
	 * Short-circuts any content output and instead shows a 404 page
	 *
	 * If debug mode is turned on an optional debugMessage can be shown
	 *
	 * @param string $attemptedUrl
	 * @param string $debugMessage
	 * @return void
	 */
	public function show404( $attemptedUrl, $debugMessage = '' )
	{
		$levels = ob_get_level();
		for( $buffers = 0; $buffers < $levels; $buffers++ ) {
			ob_end_clean();
		}
		
		$this->_capsule->template->draw(
			'global/HtmlWrapper',
			array(
				'charset' => $this->_capsule->charset,
				'title' => '404 Error',
				'jsIncludes' => array(),
				'cssIncludes' => array(),
				'body' => $this->_capsule->template->get(
					'global/404',
					array(
						'debug' => $this->_capsule->debug,
						'attemptedUrl' => $attemptedUrl,
						'debugMessage' => $debugMessage
					)
				)
			)
		);
		
		exit();
	}
	
	public function redirect( $url )
	{
		header('Location: '.$url);
		exit();
	}
	
	/**
	 * Event listener to know when a nested call has begun
	 *
	 * @param Event $evt
	 * @return void
	 */
	public function newCall( Event &$evt )
	{
		$this->_currentDepth++;
	}
	
	/**
	 * Event listener to know when a nested call has ended
	 *
	 * @param Event $evt
	 * @return void
	 */
	public function finishedCall( Event &$evt )
	{
		$this->_currentDepth--;
	}
	
	/**
	 * Event listener that waits for the entire capsule execution to finish
	 * and then processes the final output.
	 *
	 * In the case of HTML output, the global/HtmlWrapper will be used to
	 * produce the proper output. This is done in conjuction with the resource
	 * loader class to ensure proper css and js resources are loaded
	 *
	 * @param Event &$evt
	 * @return void
	 */
	public function finished( Event &$evt )
	{
		header('Content-type: '.$this->_topLevelContent);
		
		switch( $this->_topLevelContent )
		{
			case self::HTML:
				$evt->info['output'] =
					$this->_capsule->template->get(
						'global/HtmlWrapper',
						array(
							'charset' => $this->_capsule->charset,
							'title' => $this->_currentTitle,
							'jsIncludes' => $this->_capsule->resource->getJs(),
							'cssIncludes' => $this->_capsule->resource->getCss(),
							'body' => $evt->info['output']
						)
					);
				break;
			
			case self::JSON:
			case self::XML:
				break;
			
			default:
				throw new Exception(
					'Unknown content type "'.$this->_topLevelContent.'"'
				);
		}
		
	}
}