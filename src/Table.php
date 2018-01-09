<?php
namespace App;
use App\Contracts\ColumnInterface;
use App\Contracts\TableInterface;

class Table implements TableInterface {

	protected $name;
	protected $schemaName;
	protected $columns = [];
	protected $index = 0;
	protected $hash;
	protected $indexes;
	protected $foreignKeys;

	protected $currentPosition;
	protected $isModified = false;
	protected $isDeleted = false;

	public function __construct(string $name = null)
	{
		$this->name = $name;
	}

	public function setHash(string $hash)
	{
		$this->hash = $hash;
	}

	protected function setSchemaName(string $name): void {
		$this->schemaName = $name;
	}

	protected function getSchemaName(string $name) {
		return $this->schemaName;
	}

	public function parse($line) {
		if(strpos($line, '--') === 0) return;
		if(strpos($line, 'TABLE_NAME') === 0) {
			$this->name = trim(substr($line, 10));
			return;
		}
		$this->addColumn(new Column($line));
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function addColumn(ColumnInterface $column)
	{
		$column->setPosition($this->currentPosition ? 'AFTER ' . $this->currentPosition : 'FIRST');
		$this->currentPosition = $column->getName();
		$this->columns[$this->currentPosition] = $column;
	}

	public function hasColumn($name) {
		return isset($this->columns[$name]);
	}

	public function findColumn($name)
	{
		return $this->columns[$name];
	}

	public function compare(Table $table)
	{
		$added = $this->columns;
		$removed = $table->getColumns();
		$modified = [];
		foreach($added as $key => $column) {
			if(!isset($removed[$key])) {
				continue;
			}
			if(!$removed[$key]->compare($column)) {
				$modified[$key] = $column;
			}
			unset($removed[$key]);
			unset($added[$key]);
		}
		foreach($removed as $column) {
			$column->setModified();
			$this->columns[] = $column;
		}
		return compact('added', 'removed', 'modified');
	}

	public function addConstraint(ConstraintInterface $contraint)
	{
		$this->constraint = $contraint;
	}

	public function __sleep()
	{
		return [
			'columns'
		];
	}
}
