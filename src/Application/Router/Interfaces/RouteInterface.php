<?php

declare(strict_types = 1);

namespace App\Application\Router\Interfaces;

/** phpcs:ignoreFile SlevomatCodingStandard.Classes.SuperfluousExceptionNaming.SuperfluousSuffix */
interface RouteInterface {
	public const GET = 'GET';
	public const POST = 'POST';
	public const PUT = 'PUT';
	public const DELETE = 'DELETE';

	public function dispatch(string $uri, string $method): void;

	public function getPath(): string;

	public function execute(string $method, string ...$param): void;
}
