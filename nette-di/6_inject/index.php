<?php declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

class InjectPresenter extends \Nette\Application\UI\Presenter
{

	/**
	 * @var \InjectedService
	 * @inject
	 */
	public $serviceViaAnnotation;
	
	public function __construct(
		\InjectedService $serviceViaConstructor
	)
	{
		parent::__construct();
	}


	public function injectServiceViaInjectMethod(
		\InjectedService $serviceViaInjectMethod
	): void {}


	/**
	 * @param \MultiInjectedService[] $services
	 */
	public function injectMultiInjected(
		array $services
	): void {}

}

class InjectedService {}
class MultiInjectedService {}

$configurator = new \NetteConfigurator();
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/inject.neon');

$container = $configurator->createContainer();

debug__printer(
	'Metody inject - možnosti zápisu',
	[
		'createServicePresenterAsService',
	],
);
