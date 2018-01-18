<?php
namespace App;
use App\Contracts\BuilderInterface;
use App\Contracts\DriverInterface;
/**
 * Class Application
 * @author yourname
 */
class Application
{
	protected $driver;
	public function __construct(DriverInterface $driver) {
		$this->driver = $driver;
	}

	public function run(BuilderInterface $builder) {
		$builder->build();
	}
}

