<?php

declare(strict_types = 1);

namespace Tests\Unit\TodoModule\Infrastructure;

use App\TodoModule\Dto\TodoWriteDto;
use App\TodoModule\Enum\Status;
use App\TodoModule\Infrastructure\TodoRepositoryImpl;
use App\TodoModule\Repository\TodoRepository;
use App\TodoModule\ValueObject\TodoId;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TodoRepositoryImplTest extends TestCase {
	private const ROWS = [
		[
			'todo_id' => 1,
			'title' => 'První poznámka',
			'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor'
				. ' incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation'
				. ' ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in'
				. ' voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non'
				. ' proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
			'status' => 'pending',
		],
		[
			'todo_id' => 2,
			'title' => 'Druhá poznámka',
			'description' => 'Už je to uděláno, už je to hotovo.',
			'status' => 'completed',
		],
	];

	private PDO&MockObject $pdo;

	private PDOStatement&MockObject $stmt;

	private TodoRepository $todoRepository;

	public function setUp(): void {
		$this->pdo = $this->createMock(PDO::class);
		$this->stmt = $this->createMock(PDOStatement::class);
		$this->todoRepository = new TodoRepositoryImpl($this->pdo);
	}

	public function testCreate(): void {
		$row = self::ROWS[0];
		$id = \array_shift($row);
		$this->pdo->expects(self::once())
			->method('prepare')
			->willReturn($this->stmt);
		$this->stmt->expects(self::once())
			->method('execute')
			->willReturn(true);
		$this->stmt->expects(self::once())
			->method('fetchColumn')
			->willReturn($id);
		$this->stmt->expects(self::once())
			->method('rowCount')
			->willReturn(1);
		$status = Status::from($row['status']);
		$todoWriteDto = new TodoWriteDto($row['title'], $row['description'], $status);
		$todoId = $this->todoRepository->create($todoWriteDto);
		self::assertSame($id, $todoId->getValue());
	}

	public function testDelete(): void {
		$todoId = new TodoId(self::ROWS[0]['todo_id']);
		$this->pdo->expects(self::once())
			->method('prepare')
			->willReturn($this->stmt);
		$this->stmt->expects(self::once())
			->method('execute')
			->with([ 'id' => $todoId->getValue() ])
			->willReturn(true);
		$this->todoRepository->delete($todoId);
	}

	public function testFind(): void {
		$row = self::ROWS[0];
		$todoId = new TodoId($row['todo_id']);
		$this->pdo->expects(self::once())
			->method('prepare')
			->willReturn($this->stmt);
		$this->stmt->expects(self::once())
			->method('execute')
			->with([ 'id' => $todoId->getValue() ])
			->willReturn(true);
		$this->stmt->expects(self::once())
			->method('fetch')
			->willReturn($row);
		$todo = $this->todoRepository->find($todoId);
		self::assertSame($todo?->getId()->getValue(), $todoId->getValue());
		self::assertSame($todo?->getTitle()->getValue(), $row['title']);
		self::assertSame($todo?->getDescription()->getValue(), $row['description']);
		self::assertSame($todo?->getStatus()->getValue(), $row['status']);
	}

	public function testFinAll(): void {
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	public function testUpdate(): void {
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
}
