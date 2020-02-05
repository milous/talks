<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Highlight.php';

\Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT);


class NetteConfigurator extends \Nette\Configurator
{

	public function __construct()
	{
		parent::__construct();

		// shortcut for example code
		$this->setDebugMode(TRUE);
		$this->enableTracy();
	}

}

// WTF fuj kód:

function debug__printer(
	string $title,
	array $methodNames = []
): void
{
	$output = \ob_get_contents();
	\ob_end_clean();

	global $container;

	$indexFile = \debug_backtrace()[0]['file'];
	$currentDir = \dirname($indexFile);
	$currentBaseName = \basename($currentDir);

	$reflection = $container
		? new \ReflectionClass(\get_class($container))
		: NULL
	;

	echo "<h1>{$title}</h1>";

	// source
	if ($reflection) {
		$editorFile = ($_ENV['EDITOR_BASEDIR'] ?? __DIR__) . \str_replace(__DIR__, '', $indexFile);
		echo "<h3><a href='phpstorm://open/?file={$editorFile}&line=5'>Source PHP code:</a></h3>";
		echo "<pre>";

		$phpContent = \file_get_contents($indexFile);
		$phpContent = \str_replace('NetteConfigurator', 'Nette\Configurator', $phpContent);

		$startPos = \strpos($phpContent, 'bootstrap.php');
		if (\is_int($startPos)) {
			$phpContent = \Nette\Utils\Strings::substring($phpContent, $startPos + 15);
		}

		$pos = \strpos($phpContent, 'debug__printer(');
		if (\is_int($pos)) {
			$phpContent = \Nette\Utils\Strings::substring($phpContent, 0, $pos);
		}

		\printHtml($phpContent);
		echo "</pre>";
	}

	// neon
	$neonFiles = \Nette\Utils\Finder::find('*.neon')->in($currentDir);
	foreach ($neonFiles as $neonFile) {
		$baseName = \basename((string) $neonFile);
		if ($reflection) {
			echo "<h3>{$baseName}</h3>";
		}
		echo '<xmp>';
		echo \file_get_contents((string) $neonFile);
		echo '</xmp>';
	}

	// methods
	if ($reflection && \count($methodNames)) {
		echo "<h3>Generated</h3>";
		echo "<pre>";

		foreach ($methodNames as $methodName) {
			$reflectionMethod = $reflection->getMethod($methodName);

			$startLine = $reflectionMethod->getStartLine() - 1;
			$endLine = $reflectionMethod->getEndLine();
			$length = $endLine - $startLine;

			$source = \file($reflectionMethod->getFileName());
			$out = \implode("", \array_slice($source, $startLine, $length));

			\printHtml($out);
		}
		echo "</pre>";
	}

	// output
	if ($output) {
		echo "<h3>Output:</h3>";
		echo $output;
	}

	// pager
	$pager = \Nette\Utils\Finder::findDirectories('*_*')->in(__DIR__);

	$prev = 'index.html';
	$next = NULL;
	$currentNum = (int) $currentBaseName;

	foreach ($pager as $directory) {
		$baseName = \basename((string) $directory);
		$num = (int) $baseName;

		if ($num === ($currentNum - 1)) {
			$prev = $baseName;
		}

		if ($num === ($currentNum + 1)) {
			$next = $baseName;
		}
	}

	echo '<script>window.addEventListener("keydown", function (event) {';
	if ($prev !== NULL) {
		echo "if (event.key === 'ArrowLeft') { window.location.href = '/{$prev}'; }";
	}

	if ($next !== NULL) {
		echo "if (event.key === 'ArrowRight') { window.location.href = '/{$next}'; }";
	}

	echo '});</script>';
}

function printHtml(
	string $html
): void
{
	echo \str_replace(
		"\t",
		'&nbsp;&nbsp;&nbsp;&nbsp;',
		\PhpPrettify\Highlight::render($html)
	);
}

\ob_start();

global $container;
