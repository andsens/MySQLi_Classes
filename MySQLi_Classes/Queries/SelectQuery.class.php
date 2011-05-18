<?php
namespace MySQLi_Classes\Queries;
class SelectQuery extends Query implements \Iterator, \Countable {
	
	/**
	 * @var int
	 */
	private $fieldCount;
	
	/**
	 * @var int
	 */
	private $resultingRows;
	
	protected function __construct($query) {
		parent::__construct($query);
		self::$mysqli->store_result();
		$this->fieldCount = self::$mysqli->field_count;
		$this->resultingRows = $this->result->num_rows;
	}
	
	public function __get($name) {
		switch($name) {
			case 'fieldCount':
				return $this->fieldCount;
			case 'rows':
				return $this->resultingRows;
		}
	}
	
	public function count() {
		return $this->resultingRows;
	}
	
	/** @var int */
	private $iteratorPosition = -1;
	
	/** @var int */
	private $resultSetPosition = -1;
	
	/** @var mysqli_result */
	private $rows;
	
	/**
	 * @var boolean
	 */
	private $allFetched = false;
	
	public function current() {
		if($this->iteratorPosition == -1)
			$this->next();
		return $this->rows[$this->iteratorPosition];
	}
	
	public function key() {
		if($this->iteratorPosition == -1)
			$this->next();
		return $this->iteratorPosition;
	}
	
	public function next() {
		$this->iteratorPosition++;
		if($this->iteratorPosition > $this->resultSetPosition && !$this->allFetched) {
			$row = $this->result->fetch_array();
			if($row !== null) {
				$this->rows[++$this->resultSetPosition] = $row;
			} else {
				$this->allFetched = true;
				$this->result->free();
			}
		}
	}
	
	public function rewind() {
		$this->iteratorPosition = -1;
		$this->next();
	}
	
	public function valid() {
		return $this->iteratorPosition <= $this->resultSetPosition;
	}
}