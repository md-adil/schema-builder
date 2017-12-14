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

	function __construct(DriverInterface $driver = null)
	{
		$this->driver = $driver;
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
		return $this->driver->migrtionQuery()->whereNotIn('table', array_keys($tables))->get();
	}

	public function getChangedSchema($tables)
	{
		$changed = [];
		$created = [];
		foreach($tables as $table) {
			$migration = $this->getMigrationByName($table->getName());
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

	public function getMigrationByName($tableName)
	{
		return $this->driver->getMigration()->condition(['table'=> $tableName])->first();
	}

	public function newTables()
	{
		$tables = [];
		foreach($this->getFiles as $file) {
			$table = new Table();
			$table->setHash($this->fileHash($file));
			foreach($this->getLines($file) as $line) {
				$table->parse($line);
			}
			$tables[$table->getName()] = $table; 
		}
		return $tables;
	}

	public function getFileHash()
	{
		return '';
	}

	public function getFiles()
	{
		return glob(__DIR__ . '/../schemas/*.schema');
	}

	public function getLines($file)
	{
		return explode(PHP_EOL, file_get_contents($file));
	}
}
