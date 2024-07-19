<?php

declare(strict_types = 1);

namespace App\UI\API;

use App\BookModule\Dto\CreateBookDto;
use App\BookModule\Dto\UpdateBookDto;
use App\BookModule\Service\BookCrudService;
use App\BookModule\ValueObject\BookId;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;

class BooksPresenter extends Presenter {
	public function __construct(
		private BookCrudService $bookCrudService
	) {
		parent::__construct();
	}

	public function startup(): void {
		parent::startup();

		$method = $this->getHttpRequest()->getMethod();
		$action = $this->getAction();

		// Map methods to actions.
		$allowedMethods = [
			'getBooks' => 'GET',
			'createBook' => 'POST',
			'readBook' => 'GET',
			'updateBook' => 'PUT',
			'deleteBook' => 'DELETE',
		];

		if (!isset($allowedMethods[$action]) || $method === $allowedMethods[$action]) {
			return;
		}

		$this->error('Method Not Allowed', IResponse::S405_MethodNotAllowed);
	}

	public function actionBooks(): void {
		$httpMethod = $this->getHttpRequest()->getMethod();

		match ($httpMethod) {
			'GET' => $this->actionGetBooks(),
			'POST' => $this->actionCreateBook(),
			default => $this->error('Method Not Allowed', IResponse::S405_MethodNotAllowed),
		};
	}

	public function actionGetBooks(): void {
		$books = $this->bookCrudService->getBooks();
		$this->sendJson($books);
	}

	public function actionBook(string $bookId): void {
		$httpMethod = $this->getHttpRequest()->getMethod();

		match ($httpMethod) {
			'GET' => $this->actionReadBook($bookId),
			'PUT' => $this->actionUpdateBook($bookId),
			'DELETE' => $this->actionDeleteBook($bookId),
			default => $this->error('Method Not Allowed', IResponse::S405_MethodNotAllowed),
		};
	}

	public function actionCreateBook(): void {
		$body = $this->getHttpRequest()->getRawBody();
		\assert($body !== null);
		/** @var array<string, float|string> $data */
		$data = \json_decode($body, true, flags: \JSON_THROW_ON_ERROR);
		$newBookDto = new CreateBookDto(
			(string) $data['id'],
			(string) $data['author'],
			(string) $data['title'],
			(string) $data['genre'],
			(string) $data['description'],
			(float) $data['price'],
			(string) $data['publish_date']
		);
		$book = $this->bookCrudService->createBook($newBookDto);
		$this->getHttpResponse()->setCode(IResponse::S201_Created);
		$this->sendJson($book);
	}

	public function actionReadBook(string $bookId): void {
		$bookId = new BookId($bookId);
		$book = $this->bookCrudService->getBook($bookId);
		$this->sendJson($book);
	}

	public function actionUpdateBook(string $bookId): void {
		$bookId = new BookId($bookId);
		$body = $this->getHttpRequest()->getRawBody();
		\assert($body !== null);
		/** @var array<string, float|string> $data */
		$data = \json_decode($body, true, flags: \JSON_THROW_ON_ERROR);
		$updateBookDto = new UpdateBookDto(
			(string) $data['author'],
			(string) $data['title'],
			(string) $data['genre'],
			(string) $data['description'],
			(float) $data['price'],
			(string) $data['publish_date']
		);
		$book = $this->bookCrudService->updateBook($bookId, $updateBookDto);
		$this->sendJson($book);
	}

	public function actionDeleteBook(string $bookId): void {
		$bookId = new BookId($bookId);
		$this->bookCrudService->deleteBook($bookId);
		$this->getHttpResponse()->setCode(IResponse::S204_NoContent);
		$response = new TextResponse('');
		$this->sendResponse($response);
	}
}
