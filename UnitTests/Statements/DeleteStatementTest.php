<?php
use MySQLi_Classes\Statements\DeleteStatement;
use MySQLi_Classes\Statements\UpdateStatement;
use MySQLi_Classes\Exceptions\Server\UnknownErrorException;
use MySQLi_Classes\Statements\InsertStatement;
use MySQLi_Classes\Statements\SelectStatement;
use \Glucose\Entity as Entity;
use \Glucose\EntityEngine as EntityEngine;

class DeleteStatementTest extends TableComparisonTestCase {
	
	private static $mysqli;
	
	protected function setUp() {
		$this->comparisonSchema = $GLOBALS['comparisonSchema'];
		$this->actualSchema = $GLOBALS['schema'];
		self::$mysqli = $GLOBALS['mysqli'];
		
		self::$mysqli->query('START TRANSACTION;');
	}
	
	protected function getConnection() {
		return self::$mysqli;
	}
	
	public function test_P_StandardDelete() {
		$stmt = new DeleteStatement("DELETE FROM `{$this->actualSchema}`.`countries`
			WHERE `id` = ?", 'i');
		$id = 1;
		$stmt->bindAndExecute($values = array($id));
		$insertID = $this->deleteFrom('countries', array('id' => 1));
		$this->assertTablesEqual('countries');
	}
	
	protected function tearDown() {
		self::$mysqli->query('ROLLBACK;');
	}
}
