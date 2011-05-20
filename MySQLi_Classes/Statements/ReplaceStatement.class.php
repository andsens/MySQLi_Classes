<?php
namespace MySQLi_Classes\Statements;
class ReplaceStatement extends InsertStatement {
	
	protected static $queryTypeRegexp = '/^REPLACE( LOW_PRIORITY| DELAYED)? INTO/';
	
	private $assertAffectedRows = null;
	
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