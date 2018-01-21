<?php

namespace App;
use App\Contracts\ColumnInterface;
use App\Contracts\TableInterface;

class Column implements ColumnInterface {
	
	protected $name;
	protected $type;
	protected $size;
	protected $position;
	protected $renamed = false;
	protected $deleted = false;
	protected $modified = false;
	protected $props = [];

	public function __construct(string $defination = null)
	{
		if($defination) {
			$this->parse($defination);
		}
	}

	public function getType()
	{
		return $this->type;
	}
	public function getSize()
	{
		return $this->size;
	}

	public function isPrimaryKey()
	{
		if(isset($this->props['primary_key'])) {
			return $this->props['primary_key'];
		}
	}

	public function getComment()
	{
		if(isset($this->props['comment'])) {
			return $this->props['comment'];
		}
	}
	public function getDefault()
	{
		if(isset($this->props['default'])) {
			return $this->props['default'];
		}
		return;
	}
	public function isDeleted()
	{
		return $this->deleted;
	}

	public function setDeleted()
	{
		$this->deleted = true;
	}

	public function isModified()
	{
		return $this->modified;
	}

	public function setModified()
	{
		$this->modified = true;
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

	public function isAutoIncrement()
	{
		if(isset($this->props['auto_increment'])) {
			return $this->props['auto_increment'];
		}
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
		preg_match_all('/(\S+(?:\s+?)?\(.+?\)|\S+)/', $defination, $args);
		if(!$args) return;
		$args = $args[1];
		if(isset($args[0])) {
			$this->name = $args[0];
			unset($args[0]);
		}

		if(isset($args[1])) {
			list($key, $val) = $this->parseValue($args[1], 10);
			$this->type = $key;
			$this->size = $val;
			unset($args[1]);
		}

		foreach($args as $arg) {
			list($k, $v) = $this->parseValue($arg, true);
			$this->props[strtolower($k)] = $v;
		}
	}

	protected function parseValue($prop)
	{
		preg_match("/(.+)\((.+)\)/", $prop, $matched);
		if($matched) {
			return [$matched[1], $matched[2]];
		} else {
			return [ $prop, null ];
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
			'name', 'type', 'position', 'props'
		];
	}
}
