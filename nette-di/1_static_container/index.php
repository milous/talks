<?php declare(strict_types=1);

//protected $wiring = [
//	stdClass::class => [['stdClass']]
//];

// dump($container->getByType('stdClass'));

require __DIR__ . '/../bootstrap.php';

class MyContainer extends Nette\DI\Container
{
	public function createServiceStdClass(): \stdClass
	{
		// $service = new SomeClass('first', ['second']);
		$service = (object) $this->getParameters();

		return $service;
	}

	// public function getService(string $name);
	// public function getByType(string $type);
}

$container = new MyContainer([
	'first' => 'parameter',
]);

dump($container->getService('stdClass'));

debug__printer(
	'Statický kontejner'
);
