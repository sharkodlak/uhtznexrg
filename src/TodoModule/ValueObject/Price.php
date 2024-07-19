<?php

declare(strict_types = 1);

namespace App\BookModule\ValueObject;

readonly class Price {
	private const FORMAT = '%.2f';

	public function __construct(
		// NOTE: Using of float type for test assignment, in real project it should be better type.
		private float $value
	) {
	}

	public function __toString(): string {
		return \sprintf(self::FORMAT, $this->value);
	}

	public function getValue(): float {
		return $this->value;
	}
}
