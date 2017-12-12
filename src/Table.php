<?php
namespace App;
use App\Contracts\TableInterface;

class Table implements TableInterface {
	protected $name;
	protected $columns = [];
	protected $index = 0;

	public function __construct(string $name = null, array $columns = [])
	{
		$this->name = $name;
		$this->columns = $columns;
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

	public function setColumns(array $columns): void
	{
		$this->columns = $columns;
	}

	public function addColumn(Column $column)
	{
		$column->setIndex($this->index++);
		$this->columns[$column->getName()] = $column;
	}

	public function hasColumn($name) {
		return isset($this->columns[$name]);
	}

	public function findColumn($name)
	{
		return $this->columns[$name];
	}
}
