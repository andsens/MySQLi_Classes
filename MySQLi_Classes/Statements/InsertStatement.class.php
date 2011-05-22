<?php
namespace MySQLi_Classes\Statements;
class InsertStatement extends Statement {
	
	protected static $queryTypeRegexp = '/^INSERT( LOW_PRIORITY| DELAYED| HIGH_PRIORITY)?( IGNORE)? INTO/';
	
	public function __get($name) {
		switch($name) {
			case 'insertID':
				return $this->statement->insert_id;
			default:
				return parent::__get($name);
		}
	}
}