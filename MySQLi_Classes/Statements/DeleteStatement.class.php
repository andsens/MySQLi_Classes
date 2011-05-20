<?php
namespace MySQLi_Classes\Statements;
use MySQLi_Classes\Exceptions\Assertion\TooFewAffectedRowsException;
use MySQLi_Classes\Exceptions\Assertion\TooManyAffectedRowsException;

class DeleteStatement extends Statement {
	
	private $assertAffectedRows = null;
	
	protected static $queryTypeRegexp = '/^DELETE( LOW_PRIORITY)?( QUICK)?( IGNORE)?( FROM)?/';
	
	public function bindAndExecute(array $values) {
		parent::bindAndExecute($values);
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