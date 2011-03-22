<?php
namespace org\capsule\plugins\mysql;
use Exception as Exception;
use mysqli as mysqli;

class Connection
{
	protected $_mysqli;
	
	protected $_username;
	protected $_password;
	protected $_db;
	protected $_host;
	protected $_port;
	
	public function __construct(
		$username, $password, $db,
		$host = 'localhost',
		$port = '3306'
	) {
		$this->_username = $username;
		$this->_password = $password;
		$this->_db = $db;
		$this->_host = $host;
		$this->_port = $port;
	}
	
	public function get()
	{
		if( $this->_mysqli === null ) {
			$this->_connect();
		}
		return $this->_mysqli;
	}
	
	public function _connect()
	{
		$this->_mysqli = new mysqli(
			$this->_host, $this->_username, $this->_password,
			$this->_db, $this->_port
		);
		
		if( !$this->_mysqli ) {
			throw new Exception(
				'Could not connect to db at "'.$this->_host.':'.$this->_port.'"'
			);
		}
	}
	
	public function __destruct()
	{
		if( $this->_mysqli ) {
			$this->_mysqli->close();
		}
	}
}