<?php

declare(strict_types = 1);

namespace App\BookModule\Command;

use App\BookModule\Dto\CreateBookDto;
use App\BookModule\Service\BookCrudService;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:book:import', description: 'Import a book from a XML file')]
class ImportCommand extends Command {
	private const FILENAME = __DIR__ . '/../../../data/books.xml';

	public function __construct(
		private BookCrudService $bookCrudService
	) {
		parent::__construct();
	}

	protected function configure(): void {
		$this->addArgument('file', InputArgument::OPTIONAL, 'Path to the XML file', self::FILENAME);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		/** @var string|null $file */
		$file = $input->getArgument('file');

		if ($file === null) {
			$output->writeln('Please provide a path to the XML file');
			return Command::FAILURE;
		}

		$xml = \simplexml_load_file($file);

		if ($xml === false) {
			$output->writeln('Failed to load the XML file');
			return Command::FAILURE;
		}

		$output->writeln('Importing books from the XML file');
		$this->importBooks($xml);
		$output->writeln('Books imported successfully');

		return Command::SUCCESS;
	}

	private function importBooks(SimpleXMLElement $xml): void {
		foreach ($xml->book as $book) {
			$newBookDto = new CreateBookDto(
				(string) $book->attributes()->id,
				(string) $book->author,
				(string) $book->title,
				(string) $book->genre,
				(string) $book->description,
				(float) $book->price,
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				(string) $book->publish_date
			);
			$this->bookCrudService->createBook($newBookDto);
		}
	}
}
