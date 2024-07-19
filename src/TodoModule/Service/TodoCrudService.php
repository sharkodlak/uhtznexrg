<?php

declare(strict_types = 1);

namespace App\TodoModule\Service;

use App\TodoModule\Dto\CreateTodoDto;
use App\TodoModule\Dto\UpdateTodoDto;
use App\TodoModule\Entity\Todo;
use App\TodoModule\Exceptions\TodoNotFound;
use App\TodoModule\Repository\TodoRepository;
use App\TodoModule\ValueObject\TodoId;

class TodoCrudService {
	public function __construct(
		private readonly TodoRepository $todoRepository
	) {
	}

	public function createTodo(CreateTodoDto $newTodoDto): Todo {
		return $this->todoRepository->create($newTodoDto);
	}

	public function deleteTodo(TodoId $id): void {
		$this->todoRepository->delete($id);
	}

	public function getTodo(TodoId $id): Todo {
		$todo = $this->todoRepository->find($id);

		if ($todo === null) {
			throw TodoNotFound::create();
		}

		return $todo;
	}

	/**
	 * @return array<Todo>
	 */
	public function getTodos(): array {
		return $this->todoRepository->findAll();
	}

	public function updateTodo(TodoId $id, UpdateTodoDto $updateTodoDto): Todo {
		return $this->todoRepository->update($id, $updateTodoDto);
	}
}
