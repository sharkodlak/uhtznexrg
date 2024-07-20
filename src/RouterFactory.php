<?php

declare(strict_types = 1);

namespace App;

use App\Application\Router\Interfaces\RouteGroupInterface;
use App\TodoModule\Controller\TodoController;

class RouterFactory {
	public function registerRoutes(RouteGroupInterface $root): void {
		$root->group('/api', static function (RouteGroupInterface $api): void {
			$api->group('/todos', static function (RouteGroupInterface $todos): void {
				$todos->get('/{id}', TodoController::class . '::get');
				$todos->put('/{id}', TodoController::class . '::update');
				$todos->delete('/{id}', TodoController::class . '::delete');
				$todos->get('', TodoController::class . '::getAll');
				$todos->post('', TodoController::class . '::create');
			});
		});
	}
}
