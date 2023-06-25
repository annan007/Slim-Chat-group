<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

require_once '../src/database/database.php';
require_once '../src/routes/routes.php';

$app->run();
