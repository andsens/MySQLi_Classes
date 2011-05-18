<?php
use MySQLi_Classes\Statements\SelectStatement;
use \Glucose\Entity as Entity;
use \Glucose\EntityEngine as EntityEngine;

class SelectStatementTest extends PHPUnit_Framework_TestCase {
	
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
	
	public function test_P_SelectNull() {
		$stmt = new SelectStatement('SELECT NULL FROM DUAL', '');
		$stmt->assertResultingRows = 1;
		$stmt->bindAndExecute();
		$null = 'something';
		$vars = array(&$null);
		$stmt->bindResult($vars);
		$stmt->fetch();
		$this->assertNull($null);
		$this->assertEquals(1, $stmt->rows);
	}
	
	public function test_N_AssertionFailTooFew() {
		$stmt = new SelectStatement('SELECT NULL FROM DUAL', '');
		$this->setExpectedException('\MySQLi_Classes\Exceptions\Assertion\TooFewResultingRowsException', 'Failed asserting that exactly 2 rows were returned. The statement returned 1 rows.');
		$stmt->assertResultingRows = 2;
		$stmt->bindAndExecute();
	}
	
	public function test_N_AssertionFailTooMany() {
		$stmt = new SelectStatement('SELECT NULL FROM DUAL', '');
		$this->setExpectedException('\MySQLi_Classes\Exceptions\Assertion\TooManyResultingRowsException', 'Failed asserting that exactly 0 rows were returned. The statement returned 1 rows.');
		$stmt->assertResultingRows = 0;
		$stmt->bindAndExecute();
	}
	
	public function test_N_NotSelectError() {
		$this->setExpectedException('\MySQLi_Classes\Exceptions\WrongQueryTypeException', 'The query type \'MySQLi_Classes\\Statements\\SelectStatement\' is not intended for that query.');
		$stmt = new SelectStatement('DELETE FROM `cities`');
	}
	
	public function test_N_ParseError() {
		$this->setExpectedException('MySQLi_Classes\Exceptions\Server\ParseErrorException', 'You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near \'DUAL\' at line 1');
		$stmt = new SelectStatement('SELECT NULL FRM DUAL');
	}
	
	protected function tearDown() {
		self::$mysqli->query('ROLLBACK;');
	}
}
