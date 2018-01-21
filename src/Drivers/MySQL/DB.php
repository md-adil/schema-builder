<?php
namespace App\Drivers\MySQL;
use PDO;
use App\Contracts\DriverInterface;
use App\Contracts\TableInterface;
use App\Contracts\ColumnInterface;
/**
 * Class Mysql
 * @author yourname
 */
class DB implements DriverInterface
{

	protected $db;
	public function __construct($host, $username = null, $password = null)
	{
		if($host instanceOf PDO) {
			$this->db = $host;
		} else {
			if(is_array($host)) {
				$username = $host['username'];
				$password = $host['password'];
				$host = 'mysql:host=' . $host['host'] . ';dbname=' . $host['database'];
			}
			$this->db = new PDO($host, $username, $password);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}

	public function setPDO($pdo)
	{
		$this->db = $pdo;
	}

	/**
	 * Tables create / update / delete
	 */

	public function modifyTable(TableInterface $table)
	{
		$tableName = $table->getName();
		$query = "ALTER TABLE `{$tableName}`";
		$columns = [];
		foreach($table->getColumns() as $column) {
			$columns[] = $this->buildColumn($column);
		}
		$query .= implode(',', $columns);
		$this->db->query($query);
		return $this;
	}

	public function deleteTable(TableInterface $table)
	{
		$tableName = $table->getName();
		$this->db->query("DROP TABLE `{$tableName}`");
		return $this;
	}

	public function buildColumn(ColumnInterface $column)
	{
		$query = '';
		if($column->isModified()) {
			$query .= "MODIFY " . $column->getName();
		}

		$query .= sprintf("%s(%s)", $column->getType(), $column->getSize());
		if($column->isAutoIncrement()) {
			$query .= ' AUTO_INCREMENT';
		}

		if($column->isPrimaryKey()) {
			$query .= ' PRIMARY KEY';
		}
		if($default = $column->getDefault()) {
			$query .= " DEFAULT '{$default}'";
		}

		if($comment = $column->getComment()) {
			$query .= " COMMENT {$comment}";
		}
		
		return $query;
	}

	public function createTable(TableInterface $table)
	{
		$query = "CREATE TABLE `{$table->getName()}`";
		$columns = [];
		foreach($table->getColumns() as $column) {
			$name = $column->getName();
			$columns[] = "`{$name}`" . $this->buildColumn($column);
		}
		$query .= "(" . implode(',', $columns) . ")";
		$this->db->query($query);
		return $this;
	}

	public function first($tableName, $condition = [])
	{
		$conditions = $this->prepareCondition($condition);
		$stmt = $this->db->prepare("SELECT * FROM ${tableName} WHERE $conditions");
		$stmt->execute(array_values($condition));	
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

	public function prepareFields($conditions)
	{
		return implode(',', 
			array_map(function($a) {
				return "`{$a}`=?";
			}, array_keys($conditions))
		);
	}

	public function update($tableName, $field = [], $condition = [])
	{
		$conditions = $this->prepareCondition($condition);
		$fields = $this->prepareFields($field);
		$stmt = $this->db->prepare("UPDATE `{$tableName}` SET {$fields} WHERE {$conditions}");
		$values = array_merge(array_values($field), array_values($condition));
		$stmt->execute($values);
		return $this;
	}

	public function dropTable(string $table)
	{
		$this->db->query("DROP TABLE `{$table}`");
		return $this;
	}

	public function hasTable(string $tableName)
	{
		$stmt = $this->db->prepare("SHOW TABLES LIKE ?");
		$stmt->execute([$tableName]);
		$row = $stmt->fetch();
		return !!$row;
	}
}

