<?php

declare(strict_types = 1);

namespace App\BookModule\Dto;

use JsonSerializable;

readonly class CreateBookDto extends UpdateBookDto implements JsonSerializable {
	public function __construct(
		private string $id,
		string $author,
		string $title,
		string $genre,
		string $description,
		float $price,
		string $publishDate
	) {
		parent::__construct($author, $title, $genre, $description, $price, $publishDate);
	}

	public function getId(): string {
		return $this->id;
	}

	/**
	 * @return array<string, float|string>
	 */
	public function toArray(): array {
		$data = [
			'id' => $this->id,
		];
		$data += parent::toArray();

		return $data;
	}
}
