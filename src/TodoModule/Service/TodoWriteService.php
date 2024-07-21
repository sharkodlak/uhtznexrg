<?php

declare(strict_types = 1);

namespace App\TodoModule\Service;

use App\TodoModule\Dto\TodoWriteDto;
use App\TodoModule\Repository\TodoRepository;
use App\TodoModule\ValueObject\TodoId;

class TodoWriteService {
	public function __construct(
		private readonly TodoRepository $todoRepository
	) {
	}

	public function create(TodoWriteDto $newTodoDto): TodoId {
		return $this->todoRepository->create($newTodoDto);
	}

	public function delete(TodoId $id): void {
		$this->todoRepository->delete($id);
	}

	public function update(TodoId $id, TodoWriteDto $todoWriteDto): void {
		$this->todoRepository->update($id, $todoWriteDto);
	}
}
