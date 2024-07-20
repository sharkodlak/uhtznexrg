<?php

declare(strict_types = 1);

namespace App\Application;

use App\Application\Router\Router;

class Application {
	private readonly Router $router;

	public function __construct(
		private readonly Config $config
	) {
		$this->router = new Router();
	}

	public function getConfig(): Config {
		return $this->config;
	}

	public function getRouter(): Router {
		return $this->router;
	}

	public function run(): void {
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$uri = $_SERVER['REQUEST_URI'];
		// phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$method = $_SERVER['REQUEST_METHOD'];
		$this->router->dispatch($uri, $method);
	}
}
