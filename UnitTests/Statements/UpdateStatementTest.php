<?php
use MySQLi_Classes\Statements\UpdateStatement;
use MySQLi_Classes\Exceptions\Server\UnknownErrorException;
use MySQLi_Classes\Statements\InsertStatement;
use MySQLi_Classes\Statements\SelectStatement;
use \Glucose\Entity as Entity;
use \Glucose\EntityEngine as EntityEngine;

class UpdateStatementTest extends TableComparisonTestCase {
	
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
	
	public function test_P_StandardUpdate() {
		$stmt = new UpdateStatement("UPDATE `{$this->actualSchema}`.`people`
			SET `last_name` = ? WHERE `id` = ?", 'si');
		$name = 'And';
		$id = 1;
		$stmt->bindAndExecute($values = array($name, $id));
		$insertID = $this->update('people', array('id' => 1), array('last_name' => 'And'));
		$this->assertTablesEqual('people');
	}
	
	protected function tearDown() {
		self::$mysqli->query('ROLLBACK;');
	}
}
