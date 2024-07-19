<?php

declare(strict_types = 1);

namespace App\BookModule\Infrastructure;

use App\BookModule\Dto\CreateBookDto;
use App\BookModule\Dto\UpdateBookDto;
use App\BookModule\Entity\Book;
use App\BookModule\Exceptions\BookAlreadyExists;
use App\BookModule\Exceptions\BookCreateException;
use App\BookModule\Exceptions\BookRuntimeException;
use App\BookModule\Repository\BookRepository;
use App\BookModule\ValueObject\Author;
use App\BookModule\ValueObject\BookId;
use App\BookModule\ValueObject\Description;
use App\BookModule\ValueObject\Genre;
use App\BookModule\ValueObject\Price;
use App\BookModule\ValueObject\PublishDate;
use App\BookModule\ValueObject\Title;
use PDO;
use PDOStatement;

readonly class BookRepositoryImpl implements BookRepository {
	private const SELECT_FROM = 'SELECT book_id, author, title, genre, description, price, publish_date FROM books';
	private const UPDATE = 'UPDATE books
		SET author = :author,
			title = :title,
			genre = :genre,
			description = :description,
			price = :price,
			publish_date = :publish_date
		WHERE book_id = :id';

	public function __construct(
		private PDO $pdo
	) {
	}

	public function create(CreateBookDto $newBookDto): Book {
		$bookId = new BookId($newBookDto->getId());
		$book = $this->find($bookId);

		if ($book !== null) {
			throw BookAlreadyExists::create('Book already exists');
		}

		$book = $this->getBookInstanceFromDto($newBookDto);
		$stmt = $this->pdo->prepare(
			"INSERT INTO books (book_id, author, title, genre, description, price, publish_date)\n"
			. 'VALUES (:id, :author, :title, :genre, :description, :price, :publish_date)'
		);
		$stmt->execute($book->toArray());

		if ($stmt->rowCount() === 0) {
			throw BookCreateException::create('Could not create book');
		}

		return $book;
	}

	public function delete(BookId $id): void {
		$stmt = $this->pdo->prepare('DELETE FROM books WHERE book_id = :id');
		$stmt->execute(['id' => $id->getValue()]);
	}

	public function find(BookId $id): ?Book {
		$stmt = $this->pdo->prepare(self::SELECT_FROM . ' WHERE book_id = :id');
		$stmt->execute(['id' => $id->getValue()]);
		return $this->fetch($stmt);
	}

	/**
	 * @return array<Book>
	 */
	public function listByAuthor(Author $author): array {
		$stmt = $this->pdo->prepare(self::SELECT_FROM . ' WHERE author = :author');
		$stmt->execute(['author' => $author->getValue()]);
		$books = $stmt->fetchAll(PDO::FETCH_FUNC, fn (...$args) => $this->getBookInstance(...$args));

		return $books;
	}

	/**
	 * @return array<Book>
	 */
	public function findAll(): array {
		$stmt = $this->pdo->query(self::SELECT_FROM);

		if ($stmt === false) {
			throw new BookRuntimeException('Failed to fetch books');
		}

		$books = $stmt->fetchAll(PDO::FETCH_FUNC, fn (...$args) => $this->getBookInstance(...$args));

		return $books;
	}

	public function update(BookId $id, UpdateBookDto $updateBookDto): Book {
		$data = $updateBookDto->toArray();
		$data['id'] = $id->getValue();
		$stmt = $this->pdo->prepare(self::UPDATE);
		$stmt->execute($data);

		return $this->getBookInstance(
			$id->getValue(),
			$updateBookDto->getAuthor(),
			$updateBookDto->getTitle(),
			$updateBookDto->getGenre(),
			$updateBookDto->getDescription(),
			$updateBookDto->getPrice(),
			$updateBookDto->getPublishDate()
		);
	}

	private function fetch(PDOStatement $stmt): ?Book {
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		\assert(\is_array($row) || $row === false, 'Unexpected fetch result');

		if ($row === false) {
			return null;
		}

		return $this->getBookInstance(
			$row['book_id'],
			$row['author'],
			$row['title'],
			$row['genre'],
			$row['description'],
			$row['price'],
			$row['publish_date']
		);
	}

	private function getBookInstanceFromDto(CreateBookDto $newBookDto): Book {
		return $this->getBookInstance(
			$newBookDto->getId(),
			$newBookDto->getAuthor(),
			$newBookDto->getTitle(),
			$newBookDto->getGenre(),
			$newBookDto->getDescription(),
			$newBookDto->getPrice(),
			$newBookDto->getPublishDate()
		);
	}

	private function getBookInstance(
		string $bookId,
		string $author,
		string $title,
		string $genre,
		string $description,
		string|float $price,
		string $publishDate
	): Book {
		$bookIdVO = new BookId($bookId);
		$authorVO = new Author($author);
		$titleVO = new Title($title);
		$genreVO = new Genre($genre);
		$descriptionVO = new Description($description);
		$priceVO = new Price((float) $price); // phpcs:ignore Generic.Formatting.SpaceBeforeCast.NoSpace
		$publishDateVO = new PublishDate($publishDate);

		return new Book($bookIdVO, $authorVO, $titleVO, $genreVO, $descriptionVO, $priceVO, $publishDateVO);
	}
}
