<?php

declare(strict_types = 1);

namespace App\TodoModule\Controller;

use App\TodoModule\Service\TodoCrudService;

class TodoController {
	public function __construct(
		private readonly TodoCrudService $todoCrudService
	) {
	}

	public function getAll(): void {
		echo 'getAll';
	}

	public function create(): void {
		echo 'create';
	}

	public function get(string $id): void {
		echo 'get(' . $id . ')';
	}

	public function update(string $id): void {
		echo 'update(' . $id . ')';
	}

	public function delete(string $id): void {
		echo 'delete(' . $id . ')';
	}
}
