<?php

declare(strict_types = 1);

namespace App\TodoModule\Infrastructure;

use App\TodoModule\Dto\TodoWriteDto;
use App\TodoModule\Entity\Todo;
use App\TodoModule\Exceptions\TodoCreateException;
use App\TodoModule\Exceptions\TodoRuntimeException;
use App\TodoModule\Repository\TodoRepository;
use App\TodoModule\ValueObject\Description;
use App\TodoModule\ValueObject\Status;
use App\TodoModule\ValueObject\Title;
use App\TodoModule\ValueObject\TodoId;
use PDO;
use PDOStatement;

readonly class TodoRepositoryImpl implements TodoRepository {
	private const INSERT = 'INSERT INTO todos (title, description, status)
		VALUES (:title, :description, :status)
		RETURNING todo_id';
	private const SELECT = 'SELECT todo_id, title, description, status FROM todos';
	private const UPDATE = 'UPDATE todos
		SET title = :title,
			description = :description,
			status = :status
		WHERE todo_id = :id';
	private const DELETE = 'DELETE FROM todos WHERE todo_id = :id';

	public function __construct(
		private PDO $pdo
	) {
	}

	public function create(TodoWriteDto $todoWriteDto): TodoId {
		$stmt = $this->pdo->prepare(self::INSERT);
		$stmt->execute($todoWriteDto->toArray());
		$todoId = $stmt->fetchColumn();

		if ($todoId === false || $stmt->rowCount() === 0) {
			throw TodoCreateException::create('Could not create todo');
		}

		return new TodoId((int) $todoId);
	}

	public function delete(TodoId $id): bool {
		$stmt = $this->pdo->prepare(self::DELETE);
		$stmt->execute([ 'id' => $id->getValue() ]);

		return $stmt->rowCount() > 0;
	}

	public function find(TodoId $id): ?Todo {
		$stmt = $this->pdo->prepare(self::SELECT . ' WHERE todo_id = :id');
		$stmt->execute([ 'id' => $id->getValue() ]);

		return $this->fetch($stmt);
	}

	/**
	 * @return array<Todo>
	 */
	public function findAll(): array {
		$stmt = $this->pdo->query(self::SELECT);

		if ($stmt === false) {
			throw new TodoRuntimeException('Failed to fetch To-Dos.');
		}

		$todos = $stmt->fetchAll(PDO::FETCH_FUNC, fn (...$args) => $this->getTodoInstance(...$args));

		return $todos;
	}

	public function update(TodoId $id, TodoWriteDto $todoWriteDto): bool {
		$data = $todoWriteDto->toArray();
		$data['id'] = $id->getValue();
		$stmt = $this->pdo->prepare(self::UPDATE);
		$stmt->execute($data);

		return $stmt->rowCount() > 0;
	}

	private function fetch(PDOStatement $stmt): ?Todo {
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row === false) {
			return null;
		}

		if (!\is_array($row)) {
			throw new TodoRuntimeException('Failed to fetch To-Do.');
		}

		return $this->getTodoInstance($row['todo_id'], $row['title'], $row['description'], $row['status']);
	}

	private function getTodoInstance(
		int $todoId,
		string $title,
		string $description,
		string $status
	): Todo {
		$todoIdVO = new TodoId($todoId);
		$titleVO = new Title($title);
		$descriptionVO = new Description($description);
		$statusVO = new Status($status);

		return new Todo($todoIdVO, $titleVO, $descriptionVO, $statusVO);
	}
}
