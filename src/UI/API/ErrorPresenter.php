<?php

declare(strict_types = 1);

namespace App\UI\API;

use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Throwable;

class ErrorPresenter extends Presenter {
	public function run(Request $request): Response {
		$exception = $request->getParameter('exception');

		if ($exception instanceof Throwable) {
			return new JsonResponse([
				'status' => 'error',
				'message' => $exception->getMessage(),
			]);
		}

		return new JsonResponse([
			'status' => 'error',
			'message' => 'An unknown error occurred.',
		]);
	}
}
