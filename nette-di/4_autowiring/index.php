<?php declare(strict_types=1);

//$stdClass = $container->getByType(\stdClass::class);
//dump($stdClass);

require __DIR__ . '/../bootstrap.php';

class Database {

	/**
	 * @var string
	 */
	public $name;


	public function __construct(
		string $name
	)
	{
		$this->name = $name;
	}
}

class INeedDatabase {

	public function __construct(Database $database)
	{
	}
}

class INeedBackupDatabase {

	public function __construct(Database $database)
	{
	}
}

$configurator = new \NetteConfigurator();
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/autowiring.neon');

$container = $configurator->createContainer();

$database = $container->getByType(Database::class);

dump('autowired database', $database);

debug__printer(
	'Autowiring - to je oč tu běží',
	[
		'createService01',
		'createService02',
		'createService03',
	],
);
