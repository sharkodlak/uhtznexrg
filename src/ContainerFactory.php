<?php

declare(strict_types = 1);

namespace App;

use App\Application\Config;
use App\Application\DI\Container;
use App\Application\JsonHelper;
use App\Application\Router\Router;
use App\TodoModule\Controller\TodoController;
use App\TodoModule\Factory\TodoWriteDtoFactory;
use App\TodoModule\Infrastructure\TodoRepositoryImpl;
use App\TodoModule\Repository\TodoRepository;
use App\TodoModule\Service\TodoReadService;
use App\TodoModule\Service\TodoWriteService;
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

		$jsonHelper = new JsonHelper();
		$container->set($jsonHelper);

		$dsn = \sprintf('pgsql:host=%s;dbname=%s', $config->getDbHost(), $config->getDbName());
		$pdo = new ExtendedPdo($dsn, $config->getDbUser(), $config->getDbPass());
		$container->set($pdo, PDO::class);

		$todoRepository = new TodoRepositoryImpl($pdo);
		$container->set($todoRepository, TodoRepository::class);

		$todoReadService = new TodoReadService($todoRepository);
		$container->set($todoReadService);

		$todoWriteService = new TodoWriteService($todoRepository);
		$container->set($todoWriteService);

		$todoWriteDtoFactory = new TodoWriteDtoFactory();
		$container->set($todoWriteDtoFactory);

		$todoController = new TodoController($todoReadService, $todoWriteService, $todoWriteDtoFactory);
		$todoController->setJsonHelper($jsonHelper);
		$container->set($todoController);
	}
}
