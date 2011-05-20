<?php
namespace MySQLi_Classes\Statements;
use MySQLi_Classes\Exceptions\Assertion\TooFewResultingRowsException;
use MySQLi_Classes\Exceptions\Assertion\TooManyResultingRowsException;

class SelectStatement extends Statement {
	
	private $assertResultingRows = null;
	
	protected static $queryTypeRegexp = '/^SELECT/';
	
	public function bindAndExecute(array $values = null) {
		parent::bindAndExecute($values);
	}
	
	public function execute() {
		parent::execute();
		$this->statement->store_result();
		if($this->assertResultingRows !== null) {
			if($this->rows < $this->assertResultingRows)
				throw new TooFewResultingRowsException(
					"Failed asserting that exactly $this->assertResultingRows rows were returned. The statement returned $this->rows rows.");
			if($this->rows > $this->assertResultingRows)
				throw new TooManyResultingRowsException(
					"Failed asserting that exactly $this->assertResultingRows rows were returned. The statement returned $this->rows rows.");
		}
	}
	
	public function bindResult(array &$pointerArray) {
		call_user_func_array(array(&$this->statement, 'bind_result'), $pointerArray);
	}
	
	public function fetch() {
		return $this->statement->fetch();
	}
	
	public function __get($name) {
		switch($name) {
			case 'rows':
				return $this->statement->num_rows;
			case 'stmt':
				return $this->statement;
		}
	}
	
	public function __set($name, $value) {
		switch($name) {
			case 'assertResultingRows':
				$this->assertResultingRows = $value;
				return;
		}
	}
	
	public function freeResult() {
		$this->statement->free_result();
	}
}