<?php

declare(strict_types = 1);

namespace App;

use Symfony\Component\Dotenv\Dotenv;

class Bootstrap {
	private Config $config;

	public function boot(): Config {
		$dotenv = new Dotenv();
		$dotenv->load(__DIR__ . '/../.env');

		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$this->config = new Config($_ENV);

		return $this->config;
	}

	public function createApplication(): Application {
		return new Application($this->config);
	}
}
