<?php
namespace MySQLi_Classes\Queries;
use MySQLi_Classes\Connection;

use MySQLi_Classes\Exceptions\QueryNotRepeatableException;

use MySQLi_Classes\Exceptions\WrongQueryTypeException;

use MySQLi_Classes\Exceptions\QueryNotExecutedException;

use \MySQLi_Classes\Exceptions\ConnectionException;
use \MySQLi_Classes\Exceptions\ErrorException;
use MySQLi_Classes\Exceptions\ParameterCountMismatchException;
abstract class Query {
	
	protected $mysqli;
	
	protected static $queryTypeRegexp = "//";
	
	/**
	 * @var int
	 */
	private $errno;
	/**
	 * @var string
	 */
	private $error;
	
	/**
	 * @var string
	 */
	private $sql;
	
	/**
	 * @var boolean
	 */
	public $repeatable = false;
	
	/**
	 * @var mysqli_result
	 */
	protected $result;
	
	
	/**
	 * @param string $query
	 */
	public final function __construct($sql, $connectionName = 'main') {
		if(preg_match(static::$queryTypeRegexp, $sql) != 1)
			throw new WrongQueryTypeException("The query type '".get_class($this)."' is not intended for that query.");
		$this->sql = $sql;
		$this->mysqli = Connection::getInstance($connectionName)->mysqli;
	}
	
	public function run() {
		if($this->result !== null && !$this->repeatable)
			throw new QueryNotRepeatableException('This query has not been marked repeatable.');
		$this->result = $this->mysqli->query($this->sql);
		$this->errno = $this->mysqli->errno;
		$this->error = $this->mysqli->error;
		if($this->errno > 0)
			throw ErrorException::findClass($this->mysqli, __LINE__);
	}
	
	public function __get($name) {
		switch($name) {
			case 'errno':
				if($this->result === null)
					throw new QueryNotExecutedException('The query has not been executed yet.');
				return $this->errno;
			case 'error':
				if($this->result === null)
					throw new QueryNotExecutedException('The query has not been executed yet.');
				return $this->error;
			case 'sql':
				return $this->sql;
		}
	}
	
	public function __destruct() {
		if(is_object($this->result))
			$this->result->free();
	}
}