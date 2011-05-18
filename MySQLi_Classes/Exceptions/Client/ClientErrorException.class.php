<?php
namespace MySQLi_Classes\Exceptions\Client;
class ClientErrorException extends \MySQLi_Classes\Exceptions\ErrorException {
	/**
	 * Currently simply returns a new Instance of itself
	 * @todo Extend this class
	 * @param mysqli $mysqli The MySQLi instance where the error occurred
	 * @return ClientErrorException
	 */
	public static function findClass(\mysqli $mysqli, $line) {
		return new ClientErrorException($mysqli->error, $mysqli->errno, null, $line);
	}
}