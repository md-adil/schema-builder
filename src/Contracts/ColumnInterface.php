<?php
namespace App\Contracts;

interface ColumnInterface {
	public function getName();
	public function parse($defination);
}
