<?php
namespace MySQLi_Classes\Exceptions\Server;
use MySQLi_Classes\Exceptions\Server\AccessDenied as AD;

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
			case 1044: return new AD\DatabaseAccessDeniedException($mysqli->error, $mysqli->errno, null, $line);
			case 1045: return new AD\ServerAccessDeniedException($mysqli->error, $mysqli->errno, null, $line);
			case 1048: return new BadNullException($mysqli->error, $mysqli->errno, null, $line);
			case 1054: return new BadFieldException($mysqli->error, $mysqli->errno, null, $line);
			case 1062: return new DuplicateEntryException($mysqli->error, $mysqli->errno, null, $line);
			case 1064: return new ParseErrorException($mysqli->error, $mysqli->errno, null, $line);
			case 1095: return new AD\KillDeniedException($mysqli->error, $mysqli->errno, null, $line);
			case 1142: return new AD\TableAccessDeniedException($mysqli->error, $mysqli->errno, null, $line);
			case 1143: return new AD\ColumnAccessDeniedException($mysqli->error, $mysqli->errno, null, $line);
			case 1149: return new SyntaxErrorException($mysqli->error, $mysqli->errno, null, $line);
			case 1227: return new AD\SpecificAccessDeniedException($mysqli->error, $mysqli->errno, null, $line);
			case 1364: return new NoDefaultForFieldException($mysqli->error, $mysqli->errno, null, $line);
			case 1370: return new AD\ProcedureAccessDeniedException($mysqli->error, $mysqli->errno, null, $line);
			default:   return new UnknownErrorException($mysqli->error, $mysqli->errno, null, $line);
		}
	}
}