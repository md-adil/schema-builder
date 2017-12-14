<?php
namespace App;
use App\Contracts\ColumnInterface;
use App\Contracts\TableInterface;

class Table implements TableInterface {
	protected $name;
	protected $columns = [];
	protected $index = 0;
	protected $hash;
	protected $indexes;
	protected $foreignKeys;
	protected $currentPosition;

	public function __construct(string $name = null, array $columns = [])
	{
		$this->name = $name;
		$this->columns = $columns;
	}

	protected function setHash($hash)
	{
		$this->hash = $hash;
	}

	public function parse($line) {
		if(strpos($line, 'TABLE NAME') === 0) {
			$this->name = trim(substr($line, 10));
			return;
		}

		if(strpos($line, 'RENAME TABLE') === 0) {
			$this->name = trim(substr($line, 12));
			return;
		}
		
		if(strpos($line, 'RENAME') === 0) {
			list($old, $new) = array_map('trim', explode(' ', substr($line, 7)));
			if($this->hasColumn($old)) {
				$this->findColumn(trim($old))->setName(trim($new));
			} else {
				throw new \Exception("$old not found");
			}
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

	public function addIndex(IndexInterface $index) {
		$this->indexes[$index->getName()] = $index;
	}

	public function hasColumn($name) {
		return isset($this->columns[$name]);
	}

	public function findColumn($name)
	{
		return $this->columns[$name];
	}

	public function findColumnByIndex($index) {
		foreach($this->columns as $column) {
			if($column->getIndex() === $index) {
				return $column;
			}
		}
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
		return compact('added', 'removed', 'modified');
	}

	public function addConstraint(ConstraintInterface $contraint)
	{
		$this->constraint = $contraint;
	}

	public function getLastColumn()
	{
		return end($this->columns);
	}

	public function __sleep()
	{
		return [
			'columns'
		];
	}
}
