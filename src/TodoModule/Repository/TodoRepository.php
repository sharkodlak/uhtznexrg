<?php

declare(strict_types = 1);

namespace App\TodoModule\Repository;

use App\TodoModule\Dto\CreateTodoDto;
use App\TodoModule\Dto\UpdateTodoDto;
use App\TodoModule\Entity\Todo;
use App\TodoModule\ValueObject\TodoId;

interface TodoRepository {
	public function create(CreateTodoDto $newTodoDto): Todo;

	public function delete(TodoId $id): void;

	public function find(TodoId $id): ?Todo;

	/**
	 * @return array<Todo>
	 */
	public function findAll(): array;

	public function update(TodoId $id, UpdateTodoDto $updateTodoDto): Todo;
}
