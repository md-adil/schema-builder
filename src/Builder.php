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
		$this->new = $this->newTables();
	}

	public function getExistingTables()
	{

	}

	public function newTables()
	{
		$tables = [];
		foreach($this->getFiles as $file) {
			$table = new Table();
			foreach($this->getLines($file) as $line) {

				
			}
			$tables[] = $table;
		}
		return $tables;
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
