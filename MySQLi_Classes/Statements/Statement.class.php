<?php
namespace MySQLi_Classes\Statements;
use MySQLi_Classes\Exceptions\WrongQueryTypeException;

use MySQLi_Classes\Exceptions\ConnectionException;
use MySQLi_Classes\Exceptions\ErrorException;
use MySQLi_Classes\Exceptions\ParameterCountMismatchException;
abstract class Statement {
	
	private static $mysqli;
	
	/**
	 *
	 * Enter description here ...
	 * @var mysqli_stmt
	 */
	protected $statement;
	
	/**
	 *
	 * Enter description here ...
	 * @var string
	 */
	protected $paramTypes;
	
	protected static $queryTypeRegexp;
	
	public static function connect(\mysqli  $mysqli) {
		self::$mysqli = $mysqli;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param string $query
	 * @param string $types
	 * @throws MySQLi_Classes\Exceptions\ParameterCountMismatchException
	 */
	public final function __construct($query, $paramTypes = null) {
		if(preg_match(static::$queryTypeRegexp, $query) != 1)
			throw new WrongQueryTypeException("The query type '".get_class($this)."' is not intended for that query.");
		$this->statement = self::$mysqli->prepare($query);
		if(self::$mysqli->errno > 0)
			throw ErrorException::findClass(self::$mysqli, __LINE__);
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
		if(self::$mysqli->errno > 0)
			throw ErrorException::findClass(self::$mysqli, __LINE__);
	}
	
	public function execute() {
		$this->statement->execute();
	}
	
	public function __destruct() {
		$this->statement->close();
	}
}