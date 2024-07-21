<?php

declare(strict_types = 1);

namespace App\TodoModule\Factory;

use App\Exceptions\WrongInput;
use App\TodoModule\Dto\TodoWriteDto;
use App\TodoModule\Enum\Status;

class TodoWriteDtoFactory {
	/** @param array<string, string> $data */
	public function create(array $data): TodoWriteDto {
		if (!isset($data['title'])) {
			throw WrongInput::create("Property 'title' is missing");
		}

		if (!isset($data['description'])) {
			throw WrongInput::create("Property 'description' is missing");
		}

		if (!isset($data['status'])) {
			throw WrongInput::create("Property 'status' is missing");
		}

		$status = Status::tryFrom($data['status']);

		if ($status === null) {
			$msg = \sprintf(
				"Invalid status: '%s'. Must be either '%s' or '%s'.",
				$data['status'],
				Status::PENDING->value,
				Status::COMPLETED->value
			);
			throw WrongInput::create($msg);
		}

		return new TodoWriteDto($data['title'], $data['description'], $status);
	}
}
