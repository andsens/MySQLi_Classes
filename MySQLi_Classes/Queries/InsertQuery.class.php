<?php
namespace MySQLi_Classes\Queries;
class InsertQuery extends Query {
	
	/**
	 * @var int
	 */
	private $insertID;
	protected function run() {
		parent::run();
		$this->insertID = self::$mysqli->insert_id;
	}
}