<?php

namespace Drivers\MySQL;
/**
 * Class Migration
 * @author yourname
 */
class Migration
{

	protected $db;
	protected $name;
	protected $isExists;

	public function __construct($db)
	{
		$this->name = 'migration';
		$this->db = $db;

		$this->isExists = $this->db->hasTable($this->name);
	}

	public function isExists()
	{
		return $this->isExists;
	}

	public function findByName($name)
	{
		return $this->first(['name' => $name]);
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
	}
}

