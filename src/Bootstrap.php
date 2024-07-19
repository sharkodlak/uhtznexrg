<?php

declare(strict_types = 1);

namespace App;

use Nette\Bootstrap\Configurator;
use Symfony\Component\Dotenv\Dotenv;

class Bootstrap {
	public static function boot(): Configurator {
		$dotenv = new Dotenv();
		$dotenv->load(__DIR__ . '/../.env');

		$configurator = new Configurator();
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$configurator->addDynamicParameters(['env' => $_ENV]);

		$configurator->setTempDirectory(__DIR__ . '/../temp');
		$configurator->enableTracy(__DIR__ . '/../var/log');
		$configurator->addConfig(__DIR__ . '/../config/common.neon');
		$configurator->setDebugMode(true);

		return $configurator;
	}
}
