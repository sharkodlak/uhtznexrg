<?php

declare(strict_types = 1);

namespace App\BookModule\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;

readonly class PublishDate {
	private const FORMAT = 'Y-m-d';

	private DateTimeImmutable $value;

	public function __construct(
		DateTimeInterface|string $value
	) {
		if (\is_string($value)) {
			$this->value = new DateTimeImmutable($value);
			return;
		}

		$this->value = DateTimeImmutable::createFromInterface($value);
	}

	public function __toString(): string {
		return $this->value->format(self::FORMAT);
	}

	public function getValue(): DateTimeImmutable {
		return $this->value;
	}
}
