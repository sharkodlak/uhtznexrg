<?php

declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;

/** phpcs:ignoreFile SlevomatCodingStandard.Classes.SuperfluousExceptionNaming.SuperfluousSuffix */
class AppRuntimeException extends RuntimeException {
	private mixed $extra;

	public function setExtra(mixed $extra): void {
		$this->extra = $extra;
	}

	public function getExtra(): mixed {
		return $this->extra ?? null;
	}
}
