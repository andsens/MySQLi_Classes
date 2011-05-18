<?php
namespace MySQLi_Classes\Exceptions;
class ErrorException extends \RuntimeException implements Exception {
	
	public function __construct($message = "", $code = 0, $previous = null, $line = null) {
		parent::__construct($message, $code, $previous);
		if($line !== null)
			$this->line = $line;
	}
	
	/**
	 * Depending on the error code, this method returns either a ServerException or a ClientException.
	 * If no error code fits, it returns a new instance of itself
	 * @param mysqli $mysqli The MySQLi instance where the error occurred
	 * @return MySQLErrorException
	 */
	public static function findClass(\mysqli $mysqli, $line) {
		if(1000 <= $mysqli->errno && $mysqli->errno < 2000) {
			$exception = Server\ServerErrorException::findClass($mysqli, $line);
		} elseif($mysqli->errno >= 2000) {
			$exception = Client\ClientErrorException::findClass($mysqli, $line);
		} else {
			$exception = new ErrorException($mysqli->error, $mysqli->errno, null, $line);
		}
		return $exception;
	}
}