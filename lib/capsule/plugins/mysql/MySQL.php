<?php
namespace org\capsule\plugins\mysql;
use org\capsule\util\args\MultiArg as MultiArg;
use Exception as Exception;
use MySQLi_Result as MySQLi_Result;

class MySQL
{
	private $_connection;
	private $_multiArg;
	private $_lastResult;
	
	public function __construct( Connection $connection, MultiArg $multiArg )
	{
		$this->_connection = $connection;
		$this->_multiArg = $multiArg;
	}
	
	public function query( $sql, $vals = array() )
	{
		ksort($vals);
		array_reverse($vals, TRUE);
		foreach( $vals as $key => $val ) {
			$sql = str_replace(':'.$key, $this->quote($val), $sql);
		}

		capsule()->logger->log('info', $sql);
		$this->_lastResult = $this->_connection->get()->query($sql);
		if ($error = $this->_connection->get()->error) {
			capsule()->logger->log('error', $error);
		}
		if( $this->_lastResult instanceof MySQLi_Result ) {
			return $this->_fetchAll($this->_lastResult);
		}
		return $this->_lastResult;
	}
	
	public function queryRow( $sql, $vals = array() )
	{
		$result = $this->query($sql, $vals);
		if( count($result) < 1 ) {
			return FALSE;
		}
		return $result[0];
	}

	public function fetchRow( $table, $where )
	{
		return $this->queryRow(implode(' ', array(
			'SELECT * FROM `'.$table.'`',
			$this->_createWhereClause($where),
			$this->_createLimitClause(1)
		)));
	}

    public function fetchMany( $table, $where )
	{
		return $this->query(implode(' ', array(
			'SELECT * FROM `'.$table.'`',
			$this->_createWhereClause($where)
		)));
	}
	
	public function getAffectedRows()
	{
		return $this->_connection->get()->affected_rows;
	}
	
	public function getInsertId()
	{
		return $this->_connection->get()->insert_id;
	}
	
	public function insert( $table, $rows, $replaceInto = FALSE )
	{
		$insertString = 'INSERT';
		if( $replaceInto ) {
			$insertString = 'REPLACE';
		}
		
		$insertSql = $insertString . ' INTO `' . $table . '` (';
		$insertCols = array();

		foreach( $rows[0] as $key => $value ) {
			$insertCols[] = $key;
		}

		$insertSql .= implode(', ',$insertCols) . ') VALUES ';

		$rowsToInsert = array();
		foreach( $rows as $row ) {
			$rowToInsert = '';
			foreach( $insertCols as $col ) {
				$rowToInsert[]  = $this->quote($row[$col]);
			}
			$rowsToInsert[] = implode(', ',$rowToInsert);
		}

		$insertSql .= '(' . implode('),(', $rowsToInsert) . ')';

		return $this->query($insertSql);
	}
	
	public function update( $table, $values, $params = array() )
	{
		$this->_multiArg->req( $params, array(
			'where' => MultiArg::REQUIRED,
			'limit' => 1
		));

		$updateValues = array();
		foreach( $values as $col => $val ) {
			$updateValues[] = '`'.$col.'`='.$this->quote($val);
		}

		return $this->query(implode(' ', array(
			'UPDATE `'.$table.'` SET',
		   implode(', ',$updateValues),
			$this->_createWhereClause($params['where']),
			$this->_createLimitClause($params['limit'])
		)));
	}

	public function delete( $table, $params = array() )
	{
		$this->_multiArg->req( $params, array(
			'where' => MultiArg::REQUIRED,
			'limit' => 1
		));

		return $this->query(implode(' ', array(
			'DELETE FROM `'.$table.'`',
			$this->_createWhereClause($params['where']),
			$this->_createLimitClause($params['limit'])
		)));
	}
	
	public function quote( $val )
	{
		if( is_array($val) ) {
			array_walk($val, array($this, 'quote'));
			return implode(', ', $val);
		}
		if( $val === NULL ) {
			$escaped = 'NULL';
		}
		else {
			$escaped = $this->_connection->get()->real_escape_string($val);
			if( !is_numeric($escaped) ) {
				$escaped = "'".$escaped."'";
			}
		}
		return $escaped;
	}
	
	protected function _fetchAll( &$result )
	{
		$resultArray = array();
		while( ($row = $result->fetch_array(MYSQLI_ASSOC)) !== NULL ){
			$resultArray[] = $row;
		}
		$result->free();
		return $resultArray;
	}

	protected function _createWhereClause( $where_key_values )
	{
		if (count($where_key_values) < 1) {
			return '';
		}
		$where_clauses = array();
		foreach ($where_key_values as $key => $value) {
			$where_clauses[] = '`'. $key . '`=' . $this->quote($value);
		}
		return 'WHERE ' . implode(' AND ', $where_clauses);
	}

	protected function _createLimitClause( $limit ) {
		if (!$limit) {
			return '';
		}
		return 'LIMIT '.$limit;
	}
}