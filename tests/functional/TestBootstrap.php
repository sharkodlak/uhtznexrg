<?php

declare(strict_types = 1);

namespace Tests\Functional;

use App\Application\Application;
use App\Application\Config;
use App\Application\DI\Container;
use App\Bootstrap;
use PHPUnit\Framework\TestCase;

abstract class TestBootstrap extends TestCase {
	protected Application $application;

	protected Config $config;

	protected Container $container;

	protected function setUp(): void {
		$bootstrap = new Bootstrap();
		$this->config = $bootstrap->boot();
		$this->application = $bootstrap->createApplication();
		$this->container = $this->application->getContainer();
	}
}
