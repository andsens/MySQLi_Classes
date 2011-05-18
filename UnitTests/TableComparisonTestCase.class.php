<?php
abstract class TableComparisonTestCase extends PHPUnit_Framework_TestCase {
	
	private static $columnRetrievalStatement;
	
	protected $comparisonSchema;
	protected $actualSchema;
	
	/**
	 *
	 * @return mysqli
	 */
	abstract protected function getConnection();
	
	protected function insertInto($tableName, array $values) {
		$mysqli = $this->getConnection();
		$fields = '(`'.implode('`, `', array_keys($values)).'`)';
		$values = "('".implode("', '", $values)."')";
		$mysqli->query("
			INSERT INTO `{$this->comparisonSchema}`.`$tableName`
			$fields
			VALUES $values");
		return $mysqli->insert_id;
	}
	
	protected function update($tableName, array $identifier, array $updateValues) {
		$mysqli = $this->getConnection();
		$whereFields = array();
		foreach($identifier as $field => $value)
			$whereFields[] = "`$field` = '$value'";
		$where = implode(' AND ', $whereFields);
		
		$updateFields = array();
		foreach($updateValues as $field => $value)
			if($value === null)
				$updateFields[] = "`$field` = NULL";
			else
				$updateFields[] = "`$field` = '$value'";
		$update = implode(', ', $updateFields);
		$mysqli->query("
			UPDATE `{$this->comparisonSchema}`.`$tableName`
			SET $update
			WHERE $where");
		echo $mysqli->error;
	}
	
	protected function deleteFrom($tableName, array $identifier) {
		$mysqli = $this->getConnection();
		$whereFields = array();
		foreach($identifier as $field => $value)
			$whereFields[] = "`$field` = '$value'";
		$where = implode(' AND ', $whereFields);
		
		$mysqli->query("
			DELETE
			FROM `{$this->comparisonSchema}`.`$tableName`
			WHERE $where");
	}
	
	protected function selectSingle($tableName, $columnName, $identifier) {
		$mysqli = $this->getConnection();
		$whereFields = array();
		foreach($identifier as $field => $value)
			$whereFields[] = "`$field` = '$value'";
		$where = implode(' AND ', $whereFields);
		$result = $mysqli->query("
			SELECT `$columnName`
			FROM `{$this->comparisonSchema}`.`$tableName`
			WHERE $where");
		if($result->num_rows == 0)
			return null;
		$row = $result->fetch_array(MYSQLI_NUM);
		$result->free();
		return $row[0];
	}
	
	private final function prepareColumnRetrievalStatement() {
		$mysqli = $this->getConnection();
		$sql = <<<End
SELECT `columns`.`COLUMN_NAME`
FROM `information_schema`.`COLUMNS` columns
WHERE `columns`.`TABLE_SCHEMA` = ?
AND `columns`.`TABLE_NAME` = ?
ORDER BY `columns`.`ORDINAL_POSITION`
End;
		self::$columnRetrievalStatement = $mysqli->prepare($sql);
	}
	
	protected function assertTablesEqual($table, array $excludedColumns = array()) {
		if(!isset(self::$columnRetrievalStatement))
			$this->prepareColumnRetrievalStatement();
		$statement = self::$columnRetrievalStatement;
		
		$statement->bind_param('ss', $this->comparisonSchema, $table);
		$statement->bind_result($columnName);
		$statement->execute();
		$expectedColumns = array();
		while($statement->fetch())
			if(!in_array($columnName, $excludedColumns))
				$expectedColumns[] = $columnName;
		$statement->free_result();
		
		$statement->bind_param('ss', $this->actualSchema, $table);
		$statement->bind_result($columnName);
		$statement->execute();
		$actualColumns = array();
		while($statement->fetch())
			if(!in_array($columnName, $excludedColumns))
				$actualColumns[] = $columnName;
		$statement->free_result();
		
		if($expectedColumns !== $actualColumns)
			throw new Exception('The tables you want to compare do not match.');
		
		$columns = '`'.implode('`, `', $actualColumns).'`';
		$comparisonSQL = <<<End
SELECT Origin, COUNT(*) AS 'Rows', {$columns}
FROM (
	SELECT *, 'expected' AS 'Origin'
	FROM `{$this->comparisonSchema}`.`{$table}`
	UNION
	SELECT *, 'actual' AS 'Origin'
	FROM `{$this->actualSchema}`.`{$table}`
) comparison
GROUP BY {$columns}
HAVING `rows` != 2
End;
		$columns = $expectedColumns;
		array_unshift($columns, 'Rows');
		array_unshift($columns, 'Origin');
		$mysqli = $this->getConnection();
		$result = $mysqli->query($comparisonSQL);
		$rows = array();
		$maxFieldLengths = array();
		foreach($columns as $column)
			$maxFieldLengths[$column] = strlen($column);
		while($row = $result->fetch_array()) {
			foreach($columns as $column)
				$maxFieldLengths[$column] = max($maxFieldLengths[$column], strlen($row[$column]));
			$rows[] = $row;
		}
		if(count($rows) > 0) {
			$maxLineLength = 80;
			while(array_sum($maxFieldLengths)+(count($expectedColumns)-1)*3 > $maxLineLength) {
				$largest = end($columns);
				foreach($maxFieldLengths as $key => $maxFieldLength)
					if($maxFieldLengths[$largest] < $maxFieldLength)
						$largest = $key;
				$maxFieldLengths[$largest]--;
			}
			$headers = array();
			foreach($columns as $column) {
				if(strlen($column) > $maxFieldLengths[$column]) {
					$headers[$column] = substr($column, 0, max(0, $maxFieldLengths[$column]-3)).'...';
				} else {
					$headers[$column] = $column.str_repeat(' ', $maxFieldLengths[$column]-strlen($column));
				}
			}
			$header = implode(' | ', $headers);
			$excessRows = array();
			foreach($rows as $row) {
				$newRow = array();
				foreach($columns as $column) {
					if($row[$column] === null)
						$row[$column] = 'NULL';
					if(strlen($row[$column]) > $maxFieldLengths[$column]) {
						$newRow[$column] = substr($row[$column], 0, max(0, $maxFieldLengths[$column]-3)).'...';
					} else {
						$newRow[$column] = $row[$column].str_repeat(' ', $maxFieldLengths[$column]-strlen($row[$column]));
					}
				}
				$excessRows[] = implode(' | ', $newRow);
			}
			$excess = implode("\n	", $excessRows);
			$failureDescription = <<<End
Failed asserting that `{$this->comparisonSchema}`.`{$table}` and `{$this->actualSchema}`.`{$table}` are equal.
Excess rows:
	{$header}
	{$excess}
End;
			$e = new PHPUnit_Framework_ExpectationFailedException($failureDescription, NULL);
//			$this->testResult->addFailure($this, $e);
			throw $e;
		}
	}
	
}