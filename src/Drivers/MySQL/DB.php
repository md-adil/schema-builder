<?php
namespace App\Drivers\MySQL;
use PDO;
/**
 * Class Mysql
 * @author yourname
 */
class DB
{

	protected $db;
	public function __construct($host, $username = null, $password = null)
	{
		if(is_array($host)) {
			$username = $host['username'];
			$password = $host['password'];
			$host = 'mysql:host=' . $host['host'] . ';dbname=' . $host['database'];
		}
		$this->db = new PDO($host, $username, $password);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function modifyTable(Table $table)
	{
		$tableName = $table->getName();
		$query = "ALTER TABLE ${$tableName}";
		$columns = [];
		foreach($table->getColumns() as $column) {
			$columns[] = $this->buildColumn();
		}
		$query .= implode(',', $columns);
	}

	public function buildColumn(ColumnInterface $column)
	{
		$query = '';
		if($column->isModified()) {
			$query .= "MODIFY " . $column->getName();
		}

		$query .= sprintf("%s(%s)", $column->getType(), $colum->getSize());
		if($column->isAutoIncrement()) {
			$query .= ' AUTO_INCREMENT';
		}

		if($column->isPrimaryKey()) {
			$query .= ' PRIMARY KEY';
		}
		if($default = $column->getDefault()) {
			$query .= " DEFAULT '{$default}'";
		}

		if($comment = $colum->getComment()) {
			$query .= " COMMENT {$comment}";
		}
		
		return $query;
	}

	public function createTable(Table $table)
	{
		$query = "CREATE TABLE `{$table->name()}`";
		$columns = [];
		foreach($table->columns() as $colum) {
			$columns[] = $this->buildColumn($column);
		}
		$query .= "(" . implode(',', $columns) . ")";
		$this->db->query($query);
	}

	public function first($condition = [])
	{
		$conditions = $this->prepareCondition($condition);
		$stmt = $this->db->prepase("SELECT * FROM ${$this->name} WHERE $conditions");
		$stmt->execute($conditions);	
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	protected function prepareCondition($conditions)
	{
		return implode(' AND ', 
			array_map(function($a) {
				return "`{$a}`=?";
			}, array_keys($conditions))
		);
	}

	public function prepareFields()
	{
		return implode(',', 
			array_map(function($a) {
				return "`{$a}`=?";
			}, array_keys($conditions))
		);
	}

	public function update($fields = [], $conditions = [])
	{
		$conditions = $this->prepareCondition($condition);
		$fields = $this->prepareFields($fields);
		$stmt = $this->db->query("UPDATE ${$this->name} SET {$fields} WHERE {$conditions}");
		$stmt->execute(array_values($fields) + array_values($conditions));
		return $this;
	}

	public function dropTable(Table $table)
	{
		$this->db->query("DROP TABLE `{$table->name()}`");
	}

	public function hasTable(string $tableName)
	{
		$stmt = $this->db->query("SHOW TABLES LIKE '{$tableName}'");
		$row = $stmt->fetch();
		return !!$row;
	}
}

