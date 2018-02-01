<?php

namespace App;
use App\Contracts\ColumnInterface;
use App\Contracts\TableInterface;
use App\Utils\Action;

class Column implements ColumnInterface {
	
	protected $name;
	protected $type;
	protected $size;
	protected $position;
	protected $action = Action::CREATED;
	protected $renamed = false;
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
	public function getAction()
	{
		return $this->action;
	}

	public function setAction($action)
	{
		$this->action = $action;
	}

	public function isCreated()
	{
		return $this->action === Action::CREATED;
	}

	public function isPrimaryKey()
	{
		return key_exists('primary_key', $this->props);
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
		return $this->action === Action::DELETED;
	}

	public function setDeleted()
	{
		return $this->action = Action::DELETED;
	}

	public function isModified() : bool
	{
		return $this->action == Action::MODIFIED;
	}

	public function setModified()
	{
		$this->action = Action::MODIFIED;
		return $this;
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
		return key_exists('auto_increment', $this->props);
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
			list($key, $val) = $this->parseValue($args[1]);
			$this->type = $key;
			$this->size = $val;
			unset($args[1]);
		}

		foreach($args as $arg) {
			list($k, $v) = $this->parseValue($arg);
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
		$this->setAction('modified');
		$column->setAction('modified');
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

