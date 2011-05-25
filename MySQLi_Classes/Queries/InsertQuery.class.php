<?php
namespace MySQLi_Classes\Queries;
class InsertQuery extends Query {
	
	protected static $queryTypeRegexp = '/^INSERT( LOW_PRIORITY| DELAYED| HIGH_PRIORITY)?( IGNORE)? INTO/';
	
	/**
	 * @var int
	 */
	private $insertID;
	protected function run() {
		parent::run();
		$this->insertID = self::$mysqli->insert_id;
	}
	
	public function __get($name) {
		switch($name) {
			case 'insertID':
				return $this->insert_id;
			default:
				return parent::__get($name);
		}
	}
}