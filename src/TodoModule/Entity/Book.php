<?php

declare(strict_types = 1);

namespace App\BookModule\Entity;

use App\BookModule\ValueObject\Author;
use App\BookModule\ValueObject\BookId;
use App\BookModule\ValueObject\Description;
use App\BookModule\ValueObject\Genre;
use App\BookModule\ValueObject\Price;
use App\BookModule\ValueObject\PublishDate;
use App\BookModule\ValueObject\Title;
use JsonSerializable;

readonly class Book implements JsonSerializable {
	public function __construct(
		private BookId $id,
		private Author $author,
		private Title $title,
		private Genre $genre,
		private Description $description,
		private Price $price,
		private PublishDate $publishDate
	) {
	}

	public function getId(): BookId {
		return $this->id;
	}

	public function getAuthor(): Author {
		return $this->author;
	}

	public function getTitle(): Title {
		return $this->title;
	}

	public function getGenre(): Genre {
		return $this->genre;
	}

	public function getDescription(): Description {
		return $this->description;
	}

	public function getPrice(): Price {
		return $this->price;
	}

	public function getPublishDate(): PublishDate {
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
			'id' => $this->id->getValue(),
			'author' => $this->author->getValue(),
			'title' => $this->title->getValue(),
			'genre' => $this->genre->getValue(),
			'price' => $this->price->getValue(),
			'publish_date' => (string) $this->publishDate,
			'description' => $this->description->getValue(),
		];
	}
}
