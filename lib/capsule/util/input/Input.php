<?php
namespace org\capsule\util\input;
use Exception as Exception;

class Input
{
	protected $capsule;

	protected $dirty = array();

	public function __construct(&$capsule) {
		$this->capsule =& $capsule;
		$capsule->library->load('htmlpurifier', 'lib/htmlpurifier/');
	}

	public function stashDirty($key, $value) {
		$this->dirty[$key] = $value;
	}

	public function getDirty($key) {
		return $this->dirty[$key];
	}

	public function readJsonStream() {
		$json = json_decode(file_get_contents('php://input'), true);
		if (!$json) {
			throw new Exception('Invalid json submitted to php://input');
		}
		$this->stashDirty('json_stream', $json);
		return $this->cleanArray($json);
	}

	public function cleanRequest() {
		$clean = array();
		$clean['_GET'] =& $_GET;
		$clean['_POST'] =& $_POST;
		$clean['_COOKIE'] =& $_COOKIE;

		$ignore = array();
		$ignore[] =& $_REQUEST;
		$ignore[] =& $_FILES;

		foreach ($ignore as &$var) {
			$var = 'IGNORED BY org\capsule\util\input\Input';
		}

		foreach ($clean as $name => &$var) {
			$this->stashDirty($name, $var);
			$var = $this->cleanArray($var);
		}
	}

	public function cleanArray($array) {
		$cleaned = array();
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$cleaned[$this->cleanValue($key)] = $this->cleanArray($value);
			} else {
				$cleaned[$this->cleanValue($key)] = $this->cleanValue($value);
			}
		}
		return $cleaned;
	}

	public function cleanValue($value) {
		return $this->capsule->htmlpurifier->purify($value);
	}
}