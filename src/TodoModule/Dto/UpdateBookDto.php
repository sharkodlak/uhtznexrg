<?php

declare(strict_types = 1);

namespace App\BookModule\Dto;

use JsonSerializable;

readonly class UpdateBookDto implements JsonSerializable {
	public function __construct(
		private string $author,
		private string $title,
		private string $genre,
		private string $description,
		private float $price,
		private string $publishDate
	) {
	}

	public function getAuthor(): string {
		return $this->author;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getGenre(): string {
		return $this->genre;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function getPrice(): float {
		return $this->price;
	}

	public function getPublishDate(): string {
		return $this->publishDate;
	}

	public function jsonSerialize(): mixed {
		return $this->toArray();
	}

	/**
	 * @return array<string, float|string>
	 */
	public function toArray(): array {
		return [
			'author' => $this->author,
			'title' => $this->title,
			'genre' => $this->genre,
			'description' => $this->description,
			'price' => $this->price,
			'publish_date' => $this->publishDate,
		];
	}
}
