<?php

declare(strict_types = 1);

namespace App\Application\Router\Traits;

use App\Application\Router\Interfaces\RouteInterface;

/** phpcs:ignoreFile SlevomatCodingStandard.Classes.SuperfluousExceptionNaming.SuperfluousSuffix */
trait DispatchTrait {
	public function dispatch(string $uri, string $method): void {
		$pathParts = \explode('/', $this->getPath());
		$uriParts = \explode('/', $uri);
		$match = true;

		foreach ($pathParts as $part) {
			$uriPart = \array_shift($uriParts);

			if ($part !== $uriPart && !\str_starts_with($part, '{') && !\str_ends_with($part, '}')) {
				$match = false;
				break;
			}
		}

		if (!$match) {
			return;
		}

		$rest = '/' . \implode('/', $uriParts);

		if ($this instanceof RouteInterface) {
			$this->execute($method);
		} else {
			foreach ($this->getRoutes() as $route) {
				$route->dispatch($rest, $method);
			}
		}
	}
}
