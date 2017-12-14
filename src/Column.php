<?php

namespace App;
use App\Contracts\ColumnInterface;
use App\Contracts\TableInterface;

class Column implements ColumnInterface {
	protected $name;
	protected $type = 'varchar';
	protected $size = 255;
	protected $nullable = true;
	protected $primaryKey = false;
	protected $renamed = false;
	protected $autoIncrement = false;
	protected $position;
	protected $comment;

	public function __construct(string $defination = null)
	{
		if($defination) {
			$this->parse($defination);
		}
	}

	public function setPosition($position)
	{
		$this->position = $position;
	}

	public function getPosition()
	{
		return $this->position;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function isPrimaryKey()
	{
		return $this->primaryKey;
	}

	public function isAutoIncrement()
	{
		return $this->autoIncrement;
	}

	public function isNullable()
	{
		return $this->nullable;
	}

	public function setIndex($index)
	{
		$this->index = $index;
	}

	public function parse($defination)
	{
		$args = explode(' ', $defination);
		if(isset($args[0])) {
			$this->name = $args[0];
			unset($args[0]);
		}

		if(isset($args[1])) {
			$this->type = $args[1];
			unset($args[0]);
		}

		foreach($args as $arg) {
			$arg = strtolower($arg);
			if($arg === 'primary key') {
				$this->primaryKey = true;
			}
			if($arg === 'auto_increment') {
				$this->autoIncrement = true;
			}
		}
	}

	public function setName(string $name)
	{
		$this->name = $name;
	}
	public function getName()
	{
		return $this->name;
	}

	public function rename(string $name)
	{
		$this->renamed = true;
		$this->name = $name;
	}

	public function compare(Column $column)
	{
		if(serialize($this) === serialize($column)) {
			return true;
		}
		return false;
	}

	public function __wakeup()
	{

	}

	public function __sleep()
	{
		return [
			'name', 'type', 'position'
		];
	}
}
