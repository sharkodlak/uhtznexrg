<?php

declare(strict_types = 1);

namespace App\BookModule\Repository;

use App\BookModule\Dto\CreateBookDto;
use App\BookModule\Dto\UpdateBookDto;
use App\BookModule\Entity\Book;
use App\BookModule\ValueObject\Author;
use App\BookModule\ValueObject\BookId;

interface BookRepository {
	public function create(CreateBookDto $newBookDto): Book;

	public function delete(BookId $id): void;

	public function find(BookId $id): ?Book;

	/**
	 * @return array<Book>
	 */
	public function listByAuthor(Author $author): array;

	/**
	 * @return array<Book>
	 */
	public function findAll(): array;

	public function update(BookId $id, UpdateBookDto $updateBookDto): Book;
}
