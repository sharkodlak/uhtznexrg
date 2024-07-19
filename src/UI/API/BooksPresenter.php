<?php

declare(strict_types = 1);

namespace App\UI\API;

use App\TodoModule\Dto\CreateTodoDto;
use App\TodoModule\Dto\UpdateTodoDto;
use App\TodoModule\Service\TodoCrudService;
use App\TodoModule\ValueObject\TodoId;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;

class TodosPresenter extends Presenter {
	public function __construct(
		private TodoCrudService $todoCrudService
	) {
		parent::__construct();
	}

	public function startup(): void {
		parent::startup();

		$method = $this->getHttpRequest()->getMethod();
		$action = $this->getAction();

		// Map methods to actions.
		$allowedMethods = [
			'getTodos' => 'GET',
			'createTodo' => 'POST',
			'readTodo' => 'GET',
			'updateTodo' => 'PUT',
			'deleteTodo' => 'DELETE',
		];

		if (!isset($allowedMethods[$action]) || $method === $allowedMethods[$action]) {
			return;
		}

		$this->error('Method Not Allowed', IResponse::S405_MethodNotAllowed);
	}

	public function actionTodos(): void {
		$httpMethod = $this->getHttpRequest()->getMethod();

		match ($httpMethod) {
			'GET' => $this->actionGetTodos(),
			'POST' => $this->actionCreateTodo(),
			default => $this->error('Method Not Allowed', IResponse::S405_MethodNotAllowed),
		};
	}

	public function actionGetTodos(): void {
		$todos = $this->todoCrudService->getTodos();
		$this->sendJson($todos);
	}

	public function actionTodo(string $todoId): void {
		$httpMethod = $this->getHttpRequest()->getMethod();

		match ($httpMethod) {
			'GET' => $this->actionReadTodo($todoId),
			'PUT' => $this->actionUpdateTodo($todoId),
			'DELETE' => $this->actionDeleteTodo($todoId),
			default => $this->error('Method Not Allowed', IResponse::S405_MethodNotAllowed),
		};
	}

	public function actionCreateTodo(): void {
		$body = $this->getHttpRequest()->getRawBody();
		\assert($body !== null);
		/** @var array<string, float|string> $data */
		$data = \json_decode($body, true, flags: \JSON_THROW_ON_ERROR);
		$newTodoDto = new CreateTodoDto(
			(string) $data['id'],
			(string) $data['author'],
			(string) $data['title'],
			(string) $data['genre'],
			(string) $data['description'],
			(float) $data['price'],
			(string) $data['publish_date']
		);
		$todo = $this->todoCrudService->createTodo($newTodoDto);
		$this->getHttpResponse()->setCode(IResponse::S201_Created);
		$this->sendJson($todo);
	}

	public function actionReadTodo(string $todoId): void {
		$todoId = new TodoId($todoId);
		$todo = $this->todoCrudService->getTodo($todoId);
		$this->sendJson($todo);
	}

	public function actionUpdateTodo(string $todoId): void {
		$todoId = new TodoId($todoId);
		$body = $this->getHttpRequest()->getRawBody();
		\assert($body !== null);
		/** @var array<string, float|string> $data */
		$data = \json_decode($body, true, flags: \JSON_THROW_ON_ERROR);
		$updateTodoDto = new UpdateTodoDto(
			(string) $data['author'],
			(string) $data['title'],
			(string) $data['genre'],
			(string) $data['description'],
			(float) $data['price'],
			(string) $data['publish_date']
		);
		$todo = $this->todoCrudService->updateTodo($todoId, $updateTodoDto);
		$this->sendJson($todo);
	}

	public function actionDeleteTodo(string $todoId): void {
		$todoId = new TodoId($todoId);
		$this->todoCrudService->deleteTodo($todoId);
		$this->getHttpResponse()->setCode(IResponse::S204_NoContent);
		$response = new TextResponse('');
		$this->sendResponse($response);
	}
}
