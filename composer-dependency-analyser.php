<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
	->ignoreErrorsOnPackage('aura/sql', [ErrorType::UNUSED_DEPENDENCY])
	->ignoreErrorsOnPackage('contributte/console', [ErrorType::UNUSED_DEPENDENCY])
	->ignoreErrorsOnPackage('nette/nette', [ErrorType::UNUSED_DEPENDENCY])
;