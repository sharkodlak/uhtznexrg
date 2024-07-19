<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;

$configurator = Bootstrap::boot();
$container = $configurator->createContainer();
$application = $container->getService('application');

$application->run();
