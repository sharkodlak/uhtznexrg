<?php

declare(strict_types = 1);

namespace App\TodoModule\Controller;

use App\Application\Controller;
use App\Application\Exceptions\ApplicationRuntimeException;
use App\Exceptions\WrongInput;
use App\TodoModule\Factory\TodoWriteDtoFactory;
use App\TodoModule\Service\TodoReadService;
use App\TodoModule\Service\TodoWriteService;
use App\TodoModule\ValueObject\TodoId;
use Throwable;

class TodoController extends Controller {
	public function __construct(
		private readonly TodoReadService $todoReadService,
		private readonly TodoWriteService $todoWriteService,
		private readonly TodoWriteDtoFactory $todoWriteDtoFactory
	) {
	}

	public function getAll(): void {
		$todos = $this->todoReadService->getAll();
		$data = [ 'todos' => $todos ];
		$this->sendJsonResponse($data);
	}

	public function create(): void {
		try {
			$json = \file_get_contents('php://input');
			
			if ($json === false) {
				throw WrongInput::create('Invalid input. Expected an JSON object.');
			}

			$data = \json_decode($json, true);

			if (!\is_array($data)) {
				throw WrongInput::create('Invalid input. Expected an JSON object.');
			}

			$todoWriteDto = $this->todoWriteDtoFactory->create($data);
			$todoId = $this->todoWriteService->create($todoWriteDto);
			$data = [
				'id' => $todoId->getValue(),
			];
			\http_response_code(201);
			$this->sendJsonResponse($data);
		} catch (Throwable $e) {
			$this->handleException($e);
		}
	}

	public function get(string $id): void {
		try {
			$todoId = TodoId::create($id);
			$todo = $this->todoReadService->get($todoId);
			$this->sendJsonResponse($todo);
		} catch (Throwable $e) {
			$this->handleException($e);
		}
	}

	public function update(string $id): void {
		echo 'update(' . $id . ')';
	}

	public function delete(string $id): void {
		echo 'delete(' . $id . ')';
	}

	private function handleException(\Throwable $e): void {
		$status = $e instanceof ApplicationRuntimeException ? $e->getCode() : 500;
		$error = [
			'error' => $e->getMessage(),
			'code' => $e->getCode(),
		];
		\http_response_code($status);
		$this->sendJsonResponse($error);
	}
}
