<?php

declare(strict_types = 1);

namespace App\Application;

abstract class Controller {
	private JsonHelper $jsonHelper;

	public function sendJsonResponse(mixed $data): void {
		$this->jsonHelper->sendResponse($data);
	}

	public function setJsonHelper(JsonHelper $jsonHelper): void {
		$this->jsonHelper = $jsonHelper;
	}
}
