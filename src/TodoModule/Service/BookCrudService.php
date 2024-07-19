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
		private readonly TodoRepository $TodoRepository
	) {
	}

	public function createTodo(CreateTodoDto $newTodoDto): Todo {
		return $this->TodoRepository->create($newTodoDto);
	}

	public function deleteTodo(TodoId $id): void {
		$this->TodoRepository->delete($id);
	}

	public function getTodo(TodoId $id): Todo {
		$Todo = $this->TodoRepository->find($id);

		if ($Todo === null) {
			throw TodoNotFound::create();
		}

		return $Todo;
	}

	/**
	 * @return array<Todo>
	 */
	public function getTodos(): array {
		return $this->TodoRepository->findAll();
	}

	public function updateTodo(TodoId $id, UpdateTodoDto $updateTodoDto): Todo {
		return $this->TodoRepository->update($id, $updateTodoDto);
	}
}
