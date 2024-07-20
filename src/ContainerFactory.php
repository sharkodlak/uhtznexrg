<?php

declare(strict_types = 1);

namespace App;

use App\Application\Config;
use App\Application\DI\Container;
use App\Application\Router\Router;
use App\TodoModule\Controller\TodoController;
use App\TodoModule\Infrastructure\TodoRepositoryImpl;
use App\TodoModule\Repository\TodoRepository;
use App\TodoModule\Service\TodoCrudService;
use Aura\Sql\ExtendedPdo;
use PDO;

class ContainerFactory {
	public function create(Config $config): Container {
		$container = new Container();
		$this->registerServices($container, $config);

		return $container;
	}

	private function registerServices(Container $container, Config $config): void {
		$container->set(new Router());

		$dsn = \sprintf('pgsql:host=%s;dbname=%s', $config->getDbHost(), $config->getDbName());
		$pdo = new ExtendedPdo($dsn, $config->getDbUser(), $config->getDbPass());
		$container->set($pdo, PDO::class);

		$todoRepository = new TodoRepositoryImpl($pdo);
		$container->set($todoRepository, TodoRepository::class);

		$todoCrudService = new TodoCrudService($todoRepository);
		$container->set($todoCrudService);

		$container->set(new TodoController($todoCrudService));
	}
}
