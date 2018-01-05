<?php

namespace App\Drivers;
/**
 * Class Mysql
 * @author yourname
 */
class DB
{

	protected $db;
	protected $migrationName = 'migrations';
	public function __construct($host, $username, $password)
	{
		$this->db = new PDO($host, $username, $password);
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
			$query .= " DEFAULT {$default}";
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

	public function dropTable(Table $table)
	{
		$this->db->query("DROP TABLE `{$table->name()}`");
	}

	public function hasTable(string $tableName)
	{
		$stmt = $this->db->query("SHOW TABLES LIKE {$tableName}");
		$stmt->fetch();
		return !!$stmt;
	}
}

