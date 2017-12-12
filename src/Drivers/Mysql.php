<?php

namespace App\Drivers;

/**
 * Class Mysql
 * @author yourname
 */
class Mysql
{

	protected $db;
	public function __construct($host, $username, $password)
	{

		$this->db = new PDO($host, $username, $password);
	}

	public function createTable(Table $table)
	{
		
		$query = "CREATE TABLE `{$table->name()}`";
		$columns = [];
		foreach($table->columns() as $colum) {
			$query .= $column->name() . ' ' . $column->type();
			if($column->isPrimaryKey()) {
				$query .= 'PRIMARY KEY';
			}
		}
		$query .= "(" . implode(',', $columns) . ")";
		$this->db->query($query);
	}

	public function dropTable(Table $table)
	{
		$this->db->query("DROP TABLE `{$table->name()}`");
	}
}


