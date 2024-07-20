<?php

declare(strict_types = 1);

namespace App\TodoModule\Controller;

class TodoController {
	public static function getAll(): void {
		echo 'getAll';
	}

	public static function create(): void {
		echo 'create';
	}

	public static function get(string $id): void {
		echo 'get(' . $id . ')';
	}

	public static function update(string $id): void {
		echo 'update(' . $id . ')';
	}

	public static function delete(string $id): void {
		echo 'delete(' . $id . ')';
	}
}
