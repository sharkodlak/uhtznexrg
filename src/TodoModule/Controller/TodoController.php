<?php

declare(strict_types = 1);

namespace App\TodoModule\Controller;

class TodoController {
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
