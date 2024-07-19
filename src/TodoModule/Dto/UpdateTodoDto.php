<?php

declare(strict_types = 1);

namespace App\TodoModule\Dto;

use JsonSerializable;

readonly class UpdateTodoDto implements JsonSerializable {
	public function __construct(
		private string $title,
		private string $description,
		private string $status
	) {
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function getStatus(): string {
		return $this->status;
	}

	public function jsonSerialize(): mixed {
		return $this->toArray();
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array {
		return [
			'title' => $this->title,
			'description' => $this->description,
			'status' => $this->status,
		];
	}
}
