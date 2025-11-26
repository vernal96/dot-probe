<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addErrorMiddleware(true, false, false);

require __DIR__ . '/../src/routes.php';

$app->run();
