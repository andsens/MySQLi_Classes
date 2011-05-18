<?php
namespace MySQLi_Classes\Exceptions\Server;
class ServerErrorException extends \MySQLi_Classes\Exceptions\ErrorException {
	/**
	 * Depending on the error code, this method returns a very specific MySQLException
	 * If no error code fits, it returns a new instance of itself
	 * @todo Add support for more error codes
	 * @param mysqli $mysqli The MySQLi instance where the error occurred
	 * @return ServerErrorException
	 */
	public static function findClass(\mysqli $mysqli, $line) {
		switch($mysqli->errno) {
			case 1048: $exception = new BadNullException($mysqli->error, $mysqli->errno, null, $line); break;
			case 1054: $exception = new BadFieldException($mysqli->error, $mysqli->errno, null, $line); break;
			case 1062: $exception = new DuplicateEntryException($mysqli->error, $mysqli->errno, null, $line); break;
			case 1064: $exception = new ParseErrorException($mysqli->error, $mysqli->errno, null, $line); break;
			case 1149: $exception = new SyntaxErrorException($mysqli->error, $mysqli->errno, null, $line); break;
			case 1364: $exception = new NoDefaultForFieldException($mysqli->error, $mysqli->errno, null, $line); break;
			default:   $exception = new UnknownErrorException($mysqli->error, $mysqli->errno, null, $line); break;
		}
		return $exception;
	}
}