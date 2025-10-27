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

## Lazy loading and lifetimes
- Lazy loading: Services (and their nested dependencies) are not instantiated until you call `get()`. The container caches only the resolver (a closure); it does NOT cache created instances by default.
- Autowired classes: Each `get(Foo::class)` call will create a fresh `Foo` and a fresh object graph of its dependencies.
- Manual bindings: Your callable fully controls lifetime: return a new instance each time (factory) or the same instance (singleton).

Examples:

```php
use App\Container;

$container = new Container();

// Default autowiring ⇒ new instance each time
$a1 = $container->get(Service::class);
$a2 = $container->get(Service::class);
var_dump($a1 === $a2); // false

// Factory binding (new instance on every get)
$container->set(Logger::class, fn() => new Logger());
$l1 = $container->get(Logger::class);
$l2 = $container->get(Logger::class);
var_dump($l1 === $l2); // false

// Singleton/shared binding (same instance every time)
$container->set('shared_logger', (function () {
    $shared = null;
    return function () use (&$shared) {
        return $shared ??= new Logger();
    };
})());

$s1 = $container->get('shared_logger');
$s2 = $container->get('shared_logger');
var_dump($s1 === $s2); // true
```

Notes:
- If you want a class to behave as a singleton, pre-bind it with a shared resolver as shown above and request it by the same key you used in `set()`.
- Reflection is performed only once per key; subsequent `get()` calls reuse the cached resolver, keeping lazy loading efficient.

## How it works
- `Container::get($class)` reflects the class constructor and recursively resolves class-typed parameters via `get()`.
- It stores a resolver closure after the first reflection so future resolutions avoid re-reflecting.