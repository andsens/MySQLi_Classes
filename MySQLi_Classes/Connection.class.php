<?php
namespace MySQLi_Classes;
use MySQLi_Classes\Exceptions\ConnectionException;
use MySQLi_Classes\Queries\Query;
use MySQLi_Classes\Statements\Statement;

class Connection {
	
	private static $connections = array();
	
	private $mysqli;
	
	private $name;
	
	public function __construct(\MySQLi $mysqli, $name) {
		$this->mysqli = $mysqli;
		$this->name = $name;
	}
	
	public static function connect(\MySQLi $mysqli, $name ='main') {
		if($mysqli->connect_errno != 0)
			throw new ConnectionException('The MySQLi instance is not connected to a database.');
		if($mysqli->server_version == '')
			throw new ConnectionException('The MySQLi instance is not connected to a database.');
		if(array_key_exists($name, self::$connections))
			throw new ConnectionException("A connection with the name '$name' already exists.");
		return self::$connections[$name] = new self($mysqli, $name);
	}
	
	public static function getInstance($name = 'main') {
		if(!array_key_exists($name, self::$connections))
			throw new ConnectionException("A connection with the name '$name' does not exist.");
		return self::$connections[$name];
	}
	
	public function isConnected() {
		if(!isset($this->mysqli))
			return false;
		return true;
	}
	
	public function ping() {
		return $this->mysqli->ping();
	}
	
	public function disconnect() {
		return $this->close();
	}
	
	public function close() {
		return $this->mysqli->close();
	}
	
	public function __get($name) {
		switch($name) {
			case 'name':
				return $this->name;
			case 'mysqli':
				return $this->mysqli;
		}
	}
}