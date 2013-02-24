<?php
namespace org\capsule;
use org\capsule\event\Event as Event;
use org\capsule\load\Library as Library;
use org\capsule\load\Resource as Resource;
use org\capsule\load\Model as Model;
use org\capsule\event\CapsuleEvents as CapsuleEvents;
use Exception as Exception;

require_once __DIR__.'/load/Resource.php';
require_once __DIR__.'/load/Library.php';
require_once __DIR__.'/load/Model.php';

class Capsule
{
	public $charset;
	public $debug;
	public $webDir;
	public $modelDir;
	public $viewDir;
	public $controlDir;
	public $patternDir;

	public $resource;
	public $library;
	
	public function __construct( $params = array() )
	{
		$this->library = new Library($this);
		$this->library->load('multiArg', 'capsule/util/args/');

		$this->multiArg->req($params, array(
			'charset' => 'utf-8',
			'debug' => FALSE,
			'extraLibraries' => array(),
			'webDir' => __DIR__.'/../../htdocs/',
			'modelDir' => __DIR__.'/../../app/model/',
			'viewDir' => __DIR__.'/../../app/view/',
			'controlDir' => __DIR__.'/../../app/controller/',
			'patternDir' => __DIR__.'/../../app/pattern/'
		));
		
		$this->multiArg->apply($params, $this);

		$this->library->load('input', 'capsule/util/input/');
		$this->input->cleanRequest();

		$this->resource = new Resource($this->webDir);
		$this->model = new Model($this, $this->modelDir);
		$this->library->load('event', 'capsule/event/');
		$this->library->load('template', 'capsule/content/template/');
		$this->library->load('output', 'capsule/content/output/');
		
		foreach( $params['extraLibraries'] as $name => $dir ) {
			$this->library->load($name, $dir);	
		}
	}

	public function call( $url, $params = array() )
	{
		$this->multiArg->req($params, array(
			'get' => array(),
			'post' => array()
		));

		//Turn what comes after the ? into get variables
		$qMarkPos = strpos($url, '?');
		if( $qMarkPos !== FALSE ) {
			parse_str(substr($url, $qMarkPos+1), $params['get']);
			$url = substr($url, 0, $qMarkPos);
		}

		$oldGet = $_GET;
		$oldPost = $_POST;
		
		$request = function($url, $get, $post) {
			$_GET = $get;
			$_POST = $post;
			require $url;
		};
		
		$eventInfo = array(
			'url' => &$url,
			'get' => &$params['get'],
			'post' => &$params['post']
		);
		$this->event->dispatch(new Event(CapsuleEvents::NewCall, $eventInfo));
			
		$fileToLoad = $this->controlDir.$url.'.php';
		if( !$this->library->isFileLoadable($fileToLoad) ) {
			if( $url[strlen($url)-1] != '/' ) {
				$url .= '/';
			}
			$url .= 'index';
			$fileToLoad = $this->controlDir.$url.'.php';
			if( !$this->library->isFileLoadable($fileToLoad) ) {
				$this->output->show404($url);
			}
		}
		
		ob_start();
		$request($fileToLoad, $params['get'], $params['post']);
		$output = ob_get_contents();
		ob_end_clean();
		
		$_GET = $oldGet;
		$_POST = $oldPost;
		
		$eventInfo = array(
			'output' => &$output,
			'url' => $url,
			'get' => &$params['get'],
			'post' => &$params['post']
		);
		$this->event->dispatch(
			new Event(CapsuleEvents::FinishedCall, $eventInfo)
		);
		
		return $output;
	}

	public function usePattern( $patternName ) {
		$fileToLoad = $this->patternDir.$patternName.'.php';
		require_once $fileToLoad;
		return new $patternName();
	}
}