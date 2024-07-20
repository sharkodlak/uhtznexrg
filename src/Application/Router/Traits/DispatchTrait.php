<?php

declare(strict_types = 1);

namespace App\Application\Router\Traits;

use App\Application\Router\Interfaces\RouteInterface;

/** phpcs:ignoreFile SlevomatCodingStandard.Classes.SuperfluousExceptionNaming.SuperfluousSuffix */
trait DispatchTrait {
	/** @var array<string, string> $params */
	private array $params;

	public function dispatch(string $uri, string $method): void {
		$pathParts = \explode('/', $this->getPath());
		$uriParts = \explode('/', $uri);
		$match = true;

		foreach ($pathParts as $part) {
			$uriPart = \array_shift($uriParts);

			if (\str_starts_with($part, '{') && \str_ends_with($part, '}') && $uriPart !== '' && $uriPart !== null) {
				$name = substr($part, 1, -1);
				$this->params[$name] = $uriPart;
				continue;
			}

			if ($part !== $uriPart) {
				$match = false;
				break;
			}
		}

		if (!$match) {
			return;
		}

		$rest = '/' . \implode('/', $uriParts);

		if ($this instanceof RouteInterface) {
			$namedParams = $this->getParams();
			$params = array_values($namedParams);
			$this->execute($method, ...$params);
		} else {
			foreach ($this->getRoutes() as $route) {
				$route->dispatch($rest, $method);
			}
		}
	}

	/**
	 * @return array<string, string>
	 */
	private function getParams(): array {
		return $this->params ?? [];
	}
}
