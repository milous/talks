<?php declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

$configurator = new \NetteConfigurator();
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/configurator.neon');

$container = $configurator->createContainer();

dump(\CONSTANT_CREATED);

debug__printer(
	'Využití konfigurátoru při vytvoření kontejneru',
	[
		'initialize',
	]
);
