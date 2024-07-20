<?php

declare(strict_types = 1);

namespace App;

use App\Application\Application;
use App\Application\Config;
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
		$application = new Application($this->config);
		$this->registerRoutes($application);

		return $application;
	}

	public function registerRoutes(Application $application): void {
		$router = $application->getRouter();
		$root = $router->getRoot();
		$routerFactory = new RouterFactory();
		$routerFactory->registerRoutes($root);
	}
}
