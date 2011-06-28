<?php
namespace MySQLi_Classes\Statements;
use MySQLi_Classes\Connection;

use MySQLi_Classes\Exceptions\WrongQueryTypeException;
use MySQLi_Classes\Exceptions\ErrorException;
use MySQLi_Classes\Exceptions\ParameterCountMismatchException;
class Statement {
	
	private $mysqli;
	
	/**
	 *
	 * Enter description here ...
	 * @var mysqli_stmt
	 */
	protected $statement;
	
	/**
	 * @var string
	 */
	private $sql;
	
	/**
	 *
	 * Enter description here ...
	 * @var string
	 */
	protected $paramTypes;
	
	protected static $queryTypeRegexp = "//";
	
	/**
	 *
	 * Constructor for all inheriting statements. This behaviour should be the
	 * same for every inheriting class, which is why it is final.
	 * @param string $query
	 * @param string $types
	 * @throws MySQLi_Classes\Exceptions\ParameterCountMismatchException
	 * @throws MySQLi_Classes\Exceptions\ErrorException
	 */
	public final function __construct($sql, $paramTypes = null, $connectionName = 'main') {
		if(preg_match(static::$queryTypeRegexp, $sql) != 1)
			throw new WrongQueryTypeException("The query type '".get_class($this)."' is not intended for that query.");
		$this->sql = $sql;
		$this->mysqli = Connection::getInstance($connectionName)->mysqli;
		$this->statement = $this->mysqli->prepare($this->sql);
		if($this->mysqli->errno > 0)
			throw ErrorException::findClass($this->mysqli, __LINE__);
		if($paramTypes == null)
			$paramTypes = '';
		if($this->statement->param_count != strlen($this->paramTypes = $paramTypes))
			throw new ParameterCountMismatchException('There is a mismatch between the number of statement variable types and parameters.');
	}
	
	public function bindAndExecute(array &$values = null) {
		if($values == null)
			$values = array();
		$statementValues = array($this->paramTypes);
		foreach($values as &$value)
			$statementValues[] = &$value;
		$noParams = count($values);
		if(strlen($this->paramTypes) != $noParams)
			throw new ParameterCountMismatchException('There is a mismatch between the number of variables to bind and statement variable types.');
		if($noParams > 0)
			call_user_func_array(array(&$this->statement, 'bind_param'), $statementValues);
		$this->execute();
		if($this->mysqli->errno > 0)
			throw ErrorException::findClass($this->mysqli, __LINE__);
	}
	
	public function __get($name) {
		switch($name) {
			case 'stmt':
				return $this->statement;
			case 'sql':
				return $this->sql;
		}
	}
	
	public function execute() {
		$this->statement->execute();
	}
	
	public function __destruct() {
		$this->statement->close();
	}
}