<?php
namespace org\capsule\util\args;
use Exception as Exception;

class MultiArg
{
	const REQUIRED = 'REQUIRED';

	public function req( &$params, $required )
	{
		foreach( $required as $req => $default ) {
			if( isset($params[$req]) ) {
				continue;
			}

			if( $default === self::REQUIRED ) {
				throw new Exception('Paramater "'.$req.'" is required');
			}
			$params[$req] = $default;
		}
	}
	
	public function apply( &$params, &$obj, $ignoreKeys = array() )
	{
		foreach( $params as $key => &$value ) {
			if( in_array($key, $ignoreKeys) ) {
				continue;
			}
			$obj->$key =& $value;
		}
	}
}