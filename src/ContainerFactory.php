<?php

declare(strict_types = 1);

namespace App;

use App\Application\DI\Container;
use App\Application\Router\Router;
use App\TodoModule\Controller\TodoController;

class ContainerFactory {
	public function create(): Container {
		$container = new Container();
		$this->registerServices($container);

		return $container;
	}

	private function registerServices(Container $container): void {
		$container->set(new Router());
		$container->set(new TodoController());
	}
}
