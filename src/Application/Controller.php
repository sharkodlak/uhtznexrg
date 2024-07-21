<?php

declare(strict_types = 1);

namespace App\Application;

use App\Exceptions\WrongInput;

abstract class Controller {
	private JsonHelper $jsonHelper;

	public function sendJsonResponse(mixed $data): void {
		$this->jsonHelper->sendResponse($data);
	}

	public function setJsonHelper(JsonHelper $jsonHelper): void {
		$this->jsonHelper = $jsonHelper;
	}

	/**
	 * @return array<string, string>
	 */
	protected function getDataFromJsonBody(): array {
		$json = \file_get_contents('php://input');

		if ($json === false) {
			throw WrongInput::create('Invalid input. Expected an JSON object.');
		}

		$data = \json_decode($json, true);

		if (!\is_array($data)) {
			throw WrongInput::create('Invalid input. Expected an JSON object.');
		}

		return $data;
	}
}
