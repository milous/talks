<?php declare(strict_types=1);

//$presenter = $container->getByType(\InjectPresenter::class); // why not working?
//$service = $container->getByType(\InjectedService::class);

require __DIR__ . '/../bootstrap.php';

trait PresenterStartupTrait
{

	public function injectService(\InjectedService $service): void
	{
		if ( ! $this instanceof \Nette\Application\UI\Presenter) {
			throw new LogicException();
		}

		$this->onRender[] = static function (\Nette\Application\UI\Presenter $presenter) use ($service): void {
			echo \get_class($presenter) . '::onRender: ' . \get_class($service);
		};
	}
}

class InjectPresenter extends \Nette\Application\UI\Presenter
{
	use PresenterStartupTrait;
}

class InjectedService {

}

$configurator = new \NetteConfigurator();
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/trait.neon');

$container = $configurator->createContainer();
$presenter = $container->getService('presenterAsService');

$presenter->onRender($presenter); // in $presenter->run()

debug__printer(
	'Metody inject s využitím trait',
	[
		'createServicePresenterAsService',
	],
);
