<?php declare(strict_types=1);

// https://doc.nette.org/cs/3.0/dependency-injection

require __DIR__ . '/../bootstrap.php';

class stdClassWithMethod extends stdClass
{
	public function method($param): void {}
}

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/temp', TRUE);
$class = $loader->load(static function (\Nette\DI\Compiler $compiler): void {
	$compiler->loadConfig(__DIR__ . '/dynamic.neon');
});

/** @var \Nette\DI\Container $container */
$container = new $class;

$service = $container->getByType(\stdClass::class);
dump($service);

debug__printer(
	'Dynamický kontejner',
	[
		'createService01',
	]
);
