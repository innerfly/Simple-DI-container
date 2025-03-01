<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Container;
use App\UserService;

$container = new Container();
$userService = $container->get(UserService::class);
$msg = $userService->registerUser("John Doe");
var_dump($msg);