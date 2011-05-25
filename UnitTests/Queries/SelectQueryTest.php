<?php
use MySQLi_Classes\Queries\SelectQuery;
use MySQLi_Classes\Statements\SelectStatement;
use \Glucose\Entity as Entity;
use \Glucose\EntityEngine as EntityEngine;

class SelectQueryTest extends PHPUnit_Framework_TestCase {
	
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
		$query = new SelectQuery('SELECT NULL FROM DUAL');
		$query->assertResultingRows = 1;
		$query->run();
		$this->assertNull($query->fetch(0));
		$this->assertEquals(1, $query->rows);
	}
	
	public function test_P_SelectMultiple() {
		$query = new SelectQuery("SELECT `name` FROM `{$this->actualSchema}`.`countries` WHERE `name` LIKE '%y' ORDER BY `id`");
		$query->run();
		$expectedResult = array('Norway', 'Germany');
		foreach($query as $count => $row) {
			$this->assertEquals($expectedResult[$count], $row['name']);
		}
	}
	
	public function test_P_SelectRewind() {
		$query = new SelectQuery("SELECT `name` FROM `{$this->actualSchema}`.`countries` ORDER BY `id`");
		$query->run();
		$expectedResult = array('Denmark', 'Norway', 'Sweden', 'Finland', 'Iceland', 'Germany');
		foreach($query as $count => $row) {
			$this->assertEquals($expectedResult[$count], $row['name']);
			if($row['name'] == 'Finland')
				break;
		}
		foreach($query as $count => $row) {
			$this->assertEquals($expectedResult[$count], $row['name']);
		}
	}
	
	public function test_N_AssertionFailTooFew() {
		$query = new SelectQuery('SELECT NULL FROM DUAL');
		$this->setExpectedException('\MySQLi_Classes\Exceptions\Assertion\TooFewResultingRowsException', 'Failed asserting that exactly 2 rows were returned. The statement returned 1 rows.');
		$query->assertResultingRows = 2;
		$query->run();
	}
	
	public function test_N_AssertionFailTooMany() {
		$query = new SelectQuery('SELECT NULL FROM DUAL');
		$this->setExpectedException('\MySQLi_Classes\Exceptions\Assertion\TooManyResultingRowsException', 'Failed asserting that exactly 0 rows were returned. The statement returned 1 rows.');
		$query->assertResultingRows = 0;
		$query->run();
	}
	
	public function test_N_NotSelectError() {
		$this->setExpectedException('\MySQLi_Classes\Exceptions\WrongQueryTypeException', 'The query type \'MySQLi_Classes\\Queries\\SelectQuery\' is not intended for that query.');
		$query = new SelectQuery('DELETE FROM `cities`');
	}
	
	public function test_N_ParseError() {
		$this->setExpectedException('MySQLi_Classes\Exceptions\Server\ParseErrorException', 'You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near \'DUAL\' at line 1');
		$query = new SelectQuery('SELECT NULL FRM DUAL');
		$query->run();
	}
	
	public function test_N_Repeatable() {
		$this->setExpectedException('MySQLi_Classes\Exceptions\QueryNotRepeatableException', 'This query has not been marked repeatable.');
		$query = new SelectQuery('SELECT NULL FROM DUAL');
		$query->run();
		$query->run();
	}
	
	public function test_P_Repeatable() {
		$query = new SelectQuery('SELECT NULL FROM DUAL');
		$query->repeatable = true;
		$query->run();
		$query->run();
	}
	
	protected function tearDown() {
		self::$mysqli->query('ROLLBACK;');
	}
}
