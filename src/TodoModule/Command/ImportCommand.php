<?php

declare(strict_types = 1);

namespace App\TodoModule\Command;

use App\TodoModule\Dto\CreateTodoDto;
use App\TodoModule\Service\TodoCrudService;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:todo:import', description: 'Import a todo from a XML file')]
class ImportCommand extends Command {
	private const FILENAME = __DIR__ . '/../../../data/todos.xml';

	public function __construct(
		private TodoCrudService $todoCrudService
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

		$output->writeln('Importing todos from the XML file');
		$this->importTodos($xml);
		$output->writeln('Todos imported successfully');

		return Command::SUCCESS;
	}

	private function importTodos(SimpleXMLElement $xml): void {
		foreach ($xml->todo as $todo) {
			$newTodoDto = new CreateTodoDto(
				(string) $todo->attributes()->id,
				(string) $todo->author,
				(string) $todo->title,
				(string) $todo->genre,
				(string) $todo->description,
				(float) $todo->price,
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				(string) $todo->publish_date
			);
			$this->todoCrudService->createTodo($newTodoDto);
		}
	}
}
