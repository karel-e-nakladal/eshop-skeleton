<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\Bootstrap;
use Nette\Database\Explorer;

$bootstrap = new Bootstrap();

$container = $bootstrap->bootWebApplication();
$application = $container->getByType(Explorer::class);

echo("Not implemented yet.\n");