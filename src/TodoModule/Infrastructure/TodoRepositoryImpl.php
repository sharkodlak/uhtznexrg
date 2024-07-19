<?php

declare(strict_types = 1);

namespace App\TodoModule\Infrastructure;

use App\TodoModule\Dto\CreateTodoDto;
use App\TodoModule\Dto\UpdateTodoDto;
use App\TodoModule\Entity\Todo;
use App\TodoModule\Exceptions\TodoAlreadyExists;
use App\TodoModule\Exceptions\TodoCreateException;
use App\TodoModule\Exceptions\TodoRuntimeException;
use App\TodoModule\Repository\TodoRepository;
use App\TodoModule\ValueObject\Author;
use App\TodoModule\ValueObject\TodoId;
use App\TodoModule\ValueObject\Description;
use App\TodoModule\ValueObject\Genre;
use App\TodoModule\ValueObject\Price;
use App\TodoModule\ValueObject\PublishDate;
use App\TodoModule\ValueObject\Title;
use PDO;
use PDOStatement;

readonly class TodoRepositoryImpl implements TodoRepository {
	private const SELECT_FROM = 'SELECT todo_id, author, title, genre, description, price, publish_date FROM todos';
	private const UPDATE = 'UPDATE todos
		SET author = :author,
			title = :title,
			genre = :genre,
			description = :description,
			price = :price,
			publish_date = :publish_date
		WHERE todo_id = :id';

	public function __construct(
		private PDO $pdo
	) {
	}

	public function create(CreateTodoDto $newTodoDto): Todo {
		$todoId = new TodoId($newTodoDto->getId());
		$todo = $this->find($todoId);

		if ($todo !== null) {
			throw TodoAlreadyExists::create('Todo already exists');
		}

		$todo = $this->getTodoInstanceFromDto($newTodoDto);
		$stmt = $this->pdo->prepare(
			"INSERT INTO todos (todo_id, author, title, genre, description, price, publish_date)\n"
			. 'VALUES (:id, :author, :title, :genre, :description, :price, :publish_date)'
		);
		$stmt->execute($todo->toArray());

		if ($stmt->rowCount() === 0) {
			throw TodoCreateException::create('Could not create todo');
		}

		return $todo;
	}

	public function delete(TodoId $id): void {
		$stmt = $this->pdo->prepare('DELETE FROM todos WHERE todo_id = :id');
		$stmt->execute(['id' => $id->getValue()]);
	}

	public function find(TodoId $id): ?Todo {
		$stmt = $this->pdo->prepare(self::SELECT_FROM . ' WHERE todo_id = :id');
		$stmt->execute(['id' => $id->getValue()]);
		return $this->fetch($stmt);
	}

	/**
	 * @return array<Todo>
	 */
	public function listByAuthor(Author $author): array {
		$stmt = $this->pdo->prepare(self::SELECT_FROM . ' WHERE author = :author');
		$stmt->execute(['author' => $author->getValue()]);
		$todos = $stmt->fetchAll(PDO::FETCH_FUNC, fn (...$args) => $this->getTodoInstance(...$args));

		return $todos;
	}

	/**
	 * @return array<Todo>
	 */
	public function findAll(): array {
		$stmt = $this->pdo->query(self::SELECT_FROM);

		if ($stmt === false) {
			throw new TodoRuntimeException('Failed to fetch todos');
		}

		$todos = $stmt->fetchAll(PDO::FETCH_FUNC, fn (...$args) => $this->getTodoInstance(...$args));

		return $todos;
	}

	public function update(TodoId $id, UpdateTodoDto $updateTodoDto): Todo {
		$data = $updateTodoDto->toArray();
		$data['id'] = $id->getValue();
		$stmt = $this->pdo->prepare(self::UPDATE);
		$stmt->execute($data);

		return $this->getTodoInstance(
			$id->getValue(),
			$updateTodoDto->getAuthor(),
			$updateTodoDto->getTitle(),
			$updateTodoDto->getGenre(),
			$updateTodoDto->getDescription(),
			$updateTodoDto->getPrice(),
			$updateTodoDto->getPublishDate()
		);
	}

	private function fetch(PDOStatement $stmt): ?Todo {
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		\assert(\is_array($row) || $row === false, 'Unexpected fetch result');

		if ($row === false) {
			return null;
		}

		return $this->getTodoInstance(
			$row['todo_id'],
			$row['author'],
			$row['title'],
			$row['genre'],
			$row['description'],
			$row['price'],
			$row['publish_date']
		);
	}

	private function getTodoInstanceFromDto(CreateTodoDto $newTodoDto): Todo {
		return $this->getTodoInstance(
			$newTodoDto->getId(),
			$newTodoDto->getAuthor(),
			$newTodoDto->getTitle(),
			$newTodoDto->getGenre(),
			$newTodoDto->getDescription(),
			$newTodoDto->getPrice(),
			$newTodoDto->getPublishDate()
		);
	}

	private function getTodoInstance(
		string $todoId,
		string $author,
		string $title,
		string $genre,
		string $description,
		string|float $price,
		string $publishDate
	): Todo {
		$todoIdVO = new TodoId($todoId);
		$authorVO = new Author($author);
		$titleVO = new Title($title);
		$genreVO = new Genre($genre);
		$descriptionVO = new Description($description);
		$priceVO = new Price((float) $price); // phpcs:ignore Generic.Formatting.SpaceBeforeCast.NoSpace
		$publishDateVO = new PublishDate($publishDate);

		return new Todo($todoIdVO, $authorVO, $titleVO, $genreVO, $descriptionVO, $priceVO, $publishDateVO);
	}
}
