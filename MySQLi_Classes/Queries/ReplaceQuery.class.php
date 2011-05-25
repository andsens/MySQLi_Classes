<?php
namespace MySQLi_Classes\Queries;
use MySQLi_Classes\Exceptions\Assertion\TooFewAffectedRowsException;
use MySQLi_Classes\Exceptions\Assertion\TooManyAffectedRowsException;

class ReplaceQuery extends InsertQuery {
	
	protected static $queryTypeRegexp = '/^REPLACE( LOW_PRIORITY| DELAYED)? INTO/';
	
	private $assertAffectedRows = null;
	
	public function run() {
		parent::run();
		if($this->assertAffectedRows !== null) {
			if($this->rows < $this->assertAffectedRows)
				throw new TooFewAffectedRowsException(
					"Failed asserting that exactly $this->assertAffectedRows rows were affected. The statement affected $this->rows rows.");
			if($this->rows > $this->assertAffectedRows)
				throw new TooManyAffectedRowsException(
					"Failed asserting that exactly $this->assertAffectedRows rows were affected. The statement affected $this->rows rows.");
		}
	}
	
	public function __get($name) {
		switch($name) {
			case 'rows':
				return $this->statement->affected_rows;
			default:
				return parent::__get($name);
		}
	}
	
	public function __set($name, $value) {
		switch($name) {
			case 'assertAffectedRows':
				$this->assertAffectedRows = $value;
				return;
		}
	}
}