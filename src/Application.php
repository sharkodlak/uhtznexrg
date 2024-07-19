<?php

declare(strict_types = 1);

namespace App;

class Application {
	public function __construct(
		private readonly Config $config
	) {
	}

	public function run(): void {
		echo 'Hello, World!';
	}
}
