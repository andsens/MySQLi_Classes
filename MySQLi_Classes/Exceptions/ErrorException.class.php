<?php
namespace MySQLi_Classes\Exceptions;
use MySQLi_Classes\Exceptions\Client as Client;
use MySQLi_Classes\Exceptions\Server as Server;
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
	 * @return MySQLi_Classes\Exceptions\ErrorException
	 */
	public static function findClass(\mysqli $mysqli, $line) {
		if($mysqli->connect_errno > 0) {
			$errno = $mysqli->connect_errno;
			$error = $mysqli->connect_error;
		} else {
			$errno = $mysqli->errno;
			$error = $mysqli->error;
		}
		if(1000 <= $errno && $errno < 2000) {
			switch($errno) {
				case 1044: return new Server\AccessDenied\DatabaseAccessDeniedException($error, $errno, null, $line);
				case 1045: return new Server\AccessDenied\ServerAccessDeniedException($error, $errno, null, $line);
				case 1048: return new Server\BadNullException($error, $errno, null, $line);
				case 1054: return new Server\BadFieldException($error, $errno, null, $line);
				case 1062: return new Server\DuplicateEntryException($error, $errno, null, $line);
				case 1064: return new Server\ParseErrorException($error, $errno, null, $line);
				case 1095: return new Server\AccessDenied\KillDeniedException($error, $errno, null, $line);
				case 1142: return new Server\AccessDenied\TableAccessDeniedException($error, $errno, null, $line);
				case 1143: return new Server\AccessDenied\ColumnAccessDeniedException($error, $errno, null, $line);
				case 1149: return new Server\SyntaxErrorException($error, $errno, null, $line);
				case 1227: return new Server\AccessDenied\SpecificAccessDeniedException($error, $errno, null, $line);
				case 1364: return new Server\NoDefaultForFieldException($error, $errno, null, $line);
				case 1370: return new Server\AccessDenied\ProcedureAccessDeniedException($error, $errno, null, $line);
				default:   return new Server\UnknownErrorException($error, $errno, null, $line);
			}
		} elseif($errno >= 2000) {
			return new Client\ClientErrorException($error, $errno, null, $line);
		} else {
			$exception = new ErrorException($error, $errno, null, $line);
		}
		return $exception;
	}
}