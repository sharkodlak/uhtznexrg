<?php

declare(strict_types = 1);

namespace Tests\Functional\TodoModule\Controller;

use App\TodoModule\Controller\TodoController;
use PHPUnit\Framework\Attributes\Depends;
use Tests\Functional\TestBootstrap;

class TodoControllerTest extends TestBootstrap {
	private TodoController $todoController;

	public function setUp(): void {
		parent::setUp();

		$this->todoController = $this->container->get(TodoController::class);
		\http_response_code(200);
		\ob_start();
	}

	public function testCreate(): int {
		$input = \json_encode([
			'title' => 'Test title',
			'description' => 'Test description',
			'status' => 'pending',
		]);
		$this->todoController->injectInput($input);
		$this->todoController->create();
		$output = $this->getOutput();
		$data = \json_decode($output['body'], true);
		self::assertSame(201, $output['status']);
		self::assertArrayHasKey('id', $data);
		self::assertIsInt($data['id']);

		return $data['id'];
	}

	#[Depends('testCreate')]
	public function testGetAll(int $id): int {
		$this->todoController->getAll();
		$output = $this->getOutput();
		$data = \json_decode($output['body'], true);
		self::assertSame(200, $output['status']);
		self::assertArrayHasKey('todos', $data);
		self::assertIsArray($data['todos']);
		self::assertNotEmpty($data['todos']);

		foreach ($data['todos'] as $todo) {
			self::assertIsArray($todo);
			$this->assertIsTodo($todo);
		}

		return $id;
	}

	#[Depends('testGetAll')]
	public function testGet(int $id): int {
		$this->todoController->get((string) $id);
		$output = $this->getOutput();
		$data = \json_decode($output['body'], true);

		self::assertSame(200, $output['status']);
		self::assertIsArray($data);
		$this->assertIsTodo($data);

		return $id;
	}

	#[Depends('testGet')]
	public function testUpdate(int $id): int {
		$input = \json_encode([
			'title' => 'Test title updated',
			'description' => 'Test description updated',
			'status' => 'completed',
		]);
		$this->todoController->injectInput($input);
		$this->todoController->update((string) $id);
		$output = $this->getOutput();
		self::assertSame(204, $output['status']);
		self::assertEmpty($output['body']);

		return $id;
	}

	#[Depends('testUpdate')]
	public function testGetUpdated(int $id): int {
		$this->todoController->get((string) $id);
		$output = $this->getOutput();
		$data = \json_decode($output['body'], true);

		self::assertSame(200, $output['status']);
		self::assertIsArray($data);
		$this->assertIsTodo($data);
		self::assertSame('Test title updated', $data['title']);
		self::assertSame('Test description updated', $data['description']);
		self::assertSame('completed', $data['status']);

		return $id;
	}

	#[Depends('testGetUpdated')]
	public function testDelete(int $id): void {
		$this->todoController->delete((string) $id);
		$output = $this->getOutput();
		self::assertSame(204, $output['status']);
		self::assertEmpty($output['body']);
	}

	/**
	 * @return array{status: int, headers: array<string>, body: string}
	 */
	private function getOutput(): array {
		$status = \http_response_code();
		$headers = \headers_list();
		$body = \ob_get_clean();

		return [
			'status' => $status,
			'headers' => $headers,
			'body' => $body,
		];
	}

	/**
	 * @param array<string, int|string> $todo
	 */
	private function assertIsTodo(array $todo): void {
		self::assertArrayHasKey('id', $todo);
		self::assertArrayHasKey('title', $todo);
		self::assertArrayHasKey('description', $todo);
		self::assertArrayHasKey('status', $todo);

		self::assertIsInt($todo['id']);
		self::assertIsString($todo['title']);
		self::assertIsString($todo['description']);
		self::assertIsString($todo['status']);
	}
}
