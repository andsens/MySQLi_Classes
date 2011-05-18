<?php
namespace MySQLi_Classes\Statements;
class InsertStatement extends Statement {
	
	public function __get($name) {
		switch($name) {
			case 'insertID':
				return $this->statement->insert_id;
		}
	}
}