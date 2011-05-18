<?php
namespace MySQLi_Classes;
use MySQLi_Classes\Queries\Query;

use MySQLi_Classes\Statements\Statement;

class Connector {
	
	private static $mysqli;
	
	public static function connect(\MySQLi $mysqli) {
		if(isset(self::$mysqli))
			throw new ConnectionException('Already connected to a server.');
		if($mysqli->connect_errno != 0)
			throw new ConnectionException('The MySQLi instance is not connected to a database.');
		if($mysqli->server_version == '')
			throw new ConnectionException('The MySQLi instance is not connected to a database.');
		self::$mysqli = $mysqli;
		Statement::connect(self::$mysqli);
		Query::connect(self::$mysqli);
	}
	
	public static function isConnected() {
		if(!isset(self::$mysqli))
			return false;
		return true;
	}
	
	public static function ping() {
		return self::$mysqli->ping();
	}
}