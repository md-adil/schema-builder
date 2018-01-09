<?php
namespace App;
/**
* 
*/
class Builder
{
	protected $driver;
	protected $old = [];
	protected $new = [];
	protected $schemaName = 'schemas';
	protected $isSchemaExists = false;

	function __construct(DriverInterface $driver = null)
	{
		$this->driver = $driver;
		if($driver) {
			$this->isSchemaExists = $dirver->hasTable($this->schemaName);
		}
	}
	
	public function buildInstances()
	{
		$this->migration = new Migration($this->driver);
	}

	public function getMigration()
	{
		if($this->driver->hasTable($this->schemaName)) {
			return $this->driver->migration()->all();	
		} else {
			return [];
		}
	}

	public function run()
	{
		$tables = $this->newTables();
		list($created, $changed) = $this->getChangedSchema($tables);
		$this->createTables($created);
		$this->updateTables($changed);
		$this->deleteTables($this->getDeletedSchemas($tables));
	}

	public function createTables($tables)
	{
		foreach($tables as $table) {
			$this->driver->createTable($table);
		}
	}

	public function deleteTables($tables)
	{
		foreach($tables as $table) {
			$this->driver->dropTable($table);
		}
	}

	public function getDeletedSchemas($tables)
	{
	}

	public function getNewSchems($tables)
	{
		return $this->driver->migration()->exclude(array_keys($tables))->get();
	}

	public function getChangedSchema($tables)
	{
		$changed = [];
		$created = [];
		foreach($tables as $table) {
			$migration = $this->driver->migration()->findByName($table->getName());
			if(!$migration) {
				$created[] = $table;
				continue;
			}
			if($migration->hash === $table->getHash()) {
				continue;
			}
			$changed[] = $table;
		}
		return [ $created, $changed ];
	}

	public function newTables()
	{
		$tables = [];
		foreach(Schema::find(__DIR__) as $schema) {
			$table = new Table();
			$table->setHash($schema->getHash());
			foreach($schema->getLines() as $line) {
				$table->parse($line);
			}
			$tables[$table->getName()] = $table; 
		}
		return $tables;
	}
}

