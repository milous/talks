<?php declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

class SupervisorExtension extends \Nette\DI\CompilerExtension
{

	public function afterCompile(\Nette\PhpGenerator\ClassType $class): void
	{
		$container = $this->getContainerBuilder();

		foreach ($container->findByType(\IWorker::class) as $name => $serviceDefinition) {
			$method = $class->getMethod('createService' . $name);
			$method->setReturnType(\IWorker::class);

			$classType = new \Nette\PhpGenerator\ClassType();
			$classType->setImplements([\IWorker::class]);
			$classTypeMethod = $classType->addMethod('doJob');
			$classTypeMethod->setReturnType('string');
			$originBody = $method->getBody();
			$classTypeMethod->addBody(\sprintf('/** %s * origin body: %s */', \PHP_EOL, $originBody));

			if (\Nette\Utils\Strings::startsWith($originBody, 'return')) {
				$originBody = '$service = ' . \Nette\Utils\Strings::substring($originBody, 7);
			}

			$classTypeMethod->addBody($originBody);
			$classTypeMethod->addBody('return \Nette\Utils\Strings::upper($service->doJob());');

			$method->setBody(\sprintf('return new class %s;', $classType));
		}
	}

}

interface IWorker
{
	public function doJob(): string;
}

class Worker implements IWorker
{

	/**
	 * @var string
	 */
	private $job;


	public function __construct(
		string $job
	)
	{
		$this->job = $job;
	}

	public function doJob(): string
	{
		return $this->job;
	}

}

$configurator = new \NetteConfigurator();
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/extension.neon');
$configurator->defaultExtensions['supervisor'] = \SupervisorExtension::class;

$container = $configurator->createContainer();
$services = $container->findByType(\Worker::class);


foreach ($services as $name) {
	dump($container->getService($name)->doJob());
}


debug__printer(
	'Extensions - dokáží prakticky cokoliv',
	[
		'createService01',
		'createService02',
	],
);
