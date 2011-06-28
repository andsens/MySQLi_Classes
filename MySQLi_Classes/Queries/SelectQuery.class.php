<?php
namespace MySQLi_Classes\Queries;
use MySQLi_Classes\Exceptions\QueryNotExecutedException;

use MySQLi_Classes\Exceptions\Assertion\TooManyResultingRowsException;

use MySQLi_Classes\Exceptions\Assertion\TooFewResultingRowsException;

class SelectQuery extends Query implements \Iterator, \Countable {
	
	private $assertResultingRows = null;
	
	protected static $queryTypeRegexp = '/^SELECT/';
	
	/**
	 * @var int
	 */
	private $fieldCount;
	
	/**
	 * @var int
	 */
	private $resultingRows;
	
	public function run() {
		parent::run();
		$this->mysqli->store_result();
		$this->fieldCount = $this->mysqli->field_count;
		$this->resultingRows = $this->result->num_rows;
		$this->iteratorPosition = -1;
		$this->resultSetPosition = -1;
		$this->resultRows = array();
		$this->allFetched = false;
		
		if($this->assertResultingRows !== null) {
			if($this->rows < $this->assertResultingRows)
				throw new TooFewResultingRowsException(
					"Failed asserting that exactly $this->assertResultingRows rows were returned. The statement returned $this->rows rows.");
			if($this->rows > $this->assertResultingRows)
				throw new TooManyResultingRowsException(
					"Failed asserting that exactly $this->assertResultingRows rows were returned. The statement returned $this->rows rows.");
		}
	}
	
	public function __get($name) {
		switch($name) {
			case 'fieldCount':
				if($this->result === null)
					throw new QueryNotExecutedException('The query has not been executed yet.');
				return $this->fieldCount;
			case 'rows':
				if($this->result === null)
					throw new QueryNotExecutedException('The query has not been executed yet.');
				return $this->resultingRows;
			default:
				return parent::__get($name);
		}
	}
	
	public function __set($name, $value) {
		switch($name) {
			case 'assertResultingRows':
				$this->assertResultingRows = $value;
				return;
		}
	}
	
	public function count() {
		return $this->resultingRows;
	}
	
	/** @var int */
	private $iteratorPosition;
	
	/** @var int */
	private $resultSetPosition;
	
	/** @var array */
	private $resultRows;
	
	/** @var boolean */
	private $allFetched;
	
	public function current() {
		if($this->result === null)
			throw new QueryNotExecutedException('The query has not been executed yet.');
		if($this->iteratorPosition == -1)
			$this->next();
		return $this->resultRows[$this->iteratorPosition];
	}
	
	public function key() {
		if($this->result === null)
			throw new QueryNotExecutedException('The query has not been executed yet.');
		if($this->iteratorPosition == -1)
			$this->next();
		return $this->iteratorPosition;
	}
	
	public function next() {
		if($this->result === null)
			throw new QueryNotExecutedException('The query has not been executed yet.');
		$this->iteratorPosition++;
		if($this->iteratorPosition > $this->resultSetPosition && !$this->allFetched) {
			$row = $this->result->fetch_array();
			if($row !== null)
				$this->resultRows[++$this->resultSetPosition] = $row;
			else
				$this->allFetched = true;
		}
	}
	
	public function rewind() {
		if($this->result === null)
			throw new QueryNotExecutedException('The query has not been executed yet.');
		$this->iteratorPosition = -1;
		$this->next();
	}
	
	public function valid() {
		if($this->result === null)
			throw new QueryNotExecutedException('The query has not been executed yet.');
		return $this->iteratorPosition <= $this->resultSetPosition;
	}
	
	public function fetch($column = null) {
		$this->next();
		if($this->valid()) {
			$row = $this->current();
			if($column !== null) {
				if(!array_key_exists($column, $row))
					throw new UndefinedColumnException('The column does not exist.');
				return $row[$column];
			}
			return $row;
		}
		return false;
	}
}