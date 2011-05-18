<?php
namespace MySQLi_Classes\Queries;
use \MySQLi_Classes\Exceptions\ConnectionException;
use \MySQLi_Classes\Exceptions\ErrorException;
use MySQLi_Classes\Exceptions\ParameterCountMismatchException;
abstract class Query {
	
	protected static $mysqli;
	
	public static function connect(\mysqli  $mysqli) {
		self::$mysqli = $mysqli;
	}
	
	/**
	 * @var int
	 */
	protected $errno;
	/**
	 * @var string
	 */
	protected $error;
	
	/**
	 * @var string
	 */
	protected $query;
	
	/**
	 * @var mysqli_result
	 */
	protected $result;
	
	/**
	 * @param string $query
	 */
	public function __construct($query) {
		$this->query = $query;
		$this->result = self::$mysqli->query($this->query);
		$this->errno = self::$mysqli->errno;
		$this->error = self::$mysqli->error;
		if($this->errno > 0)
			throw ErrorException::findClass(self::$mysqli, __LINE__);
	}
	
	public function __get($name) {
		switch($name) {
			case 'errno':
				return $this->errno;
			case 'error':
				return $this->error;
		}
	}
	
	public function __destruct() {
		$this->result->free();
	}
}