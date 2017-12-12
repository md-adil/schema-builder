<?php
namespace App\Contracts;

interface TableInterface {
	public function getName();
	public function setName(string $name);
}