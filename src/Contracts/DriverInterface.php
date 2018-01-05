<?php

namespace App\Contracts;

interface DriverInterface {
	public function createTable(TableInterface $table);
	public function dropTable(TableInterface $table);

	public function hasTable(string $tableName);

	public function modifyTable(TableInterface $table);
}
