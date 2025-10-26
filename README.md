# Simple DI Container

A tiny, reflection-based Dependency Injection container with zero external dependencies. It supports constructor autowiring for class-typed dependencies and simple manual bindings via callables.

## Requirements
- PHP 8.2+
- Composer (for autoloading)

## Installation
Clone the repository and install the Composer autoloader (no packages are required, but autoload files will be generated if needed):

```bash
git clone git@github.com:innerfly/Simple-DI-container.git
cd Simple-DI-container
composer install
```

## Usage example

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use App\Container;

$container = new Container();

// 1) Auto-wiring by constructor type-hints
class Logger {
    public function log($message){
        echo $message;
    }
}

class Service {
    public function __construct(public Logger $logger) {}
}

$service = $container->get(Service::class);
$service->logger->log('Hello world!');

// 2) Manual binding via callable (factory semantics: new instance each time)
$container->set('now', fn() => new DateTimeImmutable());

echo($container->get('now')->format('Y-m-d H:i:s'));

```

## How it works (brief)
- `Container::get($class)` reflects the class constructor and recursively resolves class-typed parameters via `get()`.
- It stores a resolver closure after the first reflection so future resolutions avoid re-reflecting.
