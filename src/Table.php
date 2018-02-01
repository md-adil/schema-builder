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
	protected $action = 'created';

	public function __construct(string $name = null)
	{
		$this->name = $name;
	}

	public function isCreated()
	{
		return $this->action === 'created';
	}

	public function isDeleted()
	{
		return $this->action === 'deleted';
	}

	public function isModified(): bool
	{
		return (bool)$this->action === 'midified';
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
		$column->setIndex($this->index);
		$this->currentPosition = $column->getName();
		$this->columns[$this->currentPosition] = $column;
		$this->index++;
		return $this;
	}

	public function hasColumn($name) {
		return isset($this->columns[$name]);
	}

	public function findColumn($name)
	{
		return $this->columns[$name];
	}

	public function findByIndex($index)
	{
		$keys = array_keys($this->columns);
		return $this->columns[$keys[0]];
	}

	public function compare(Table $table)
	{
		$columns = $table->getColumns();
		foreach($this->getColumns() as $key => $column) {
			if(!isset($columns[$key])) {
				$column->setDeleted();
				$this->addColumn($column);
				continue;
			}
			if(!$columns[$key]->compare($column)) {
				$this->findColumn($key)->setModified();
			}
			unset($columns[$key]);
		}

		foreach($columns as $column) {
			$this->addColumn($column);
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
