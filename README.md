# Simple DI Container (PHP 8.2)

A tiny, reflection-based Dependency Injection container with zero external dependencies. It supports constructor autowiring for class-typed dependencies and simple manual bindings via callables.

## Requirements
- PHP 8.2+
- Composer (for autoloading)

## Installation
Clone the repository and install the Composer autoloader (no packages are required, but autoload files will be generated if needed):

```bash
git clone git@github.com:innerfly/Simple-DI-container.git
cd Simple-DI-container
composer dump-autoload
```

## Usage example

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use App\Container;

$container = new Container();

// 1) Manual binding via callable (factory semantics: new instance each time)
$container->set('now', fn() => new DateTimeImmutable());
$first = $container->get('now');
$second = $container->get('now');
// $first !== $second

// 2) Auto-wiring by constructor type-hints
class Logger {}
class Service {
    public function __construct(public Logger $logger) {}
}

$service = $container->get(Service::class);
// $service is a Service with a Logger injected automatically

// Note: On the first auto-wired resolution per class, the container echoes
// "Setting <ClassName>" once, then caches the resolver for subsequent calls.

// 3) Singleton-style binding (manually keep/reuse the same instance)
$container->set(Logger::class, (function () {
    $instance = null;
    return function () use (&$instance) {
        return $instance ??= new Logger();
    };
})());

$l1 = $container->get(Logger::class);
$l2 = $container->get(Logger::class);
// $l1 === $l2 (same instance returned)
```

## How it works (brief)
- `Container::get($class)` reflects the class constructor and recursively resolves class-typed parameters via `get()`.
- It stores a resolver closure after the first reflection so future resolutions avoid re-reflecting.

## Limitations (by design for simplicity)
- Only constructor injection is supported.
- Only class-typed parameters are autowired (scalar params, union/intersection types, generics, etc. are not supported).
- No detection of circular dependencies.
- No contextual bindings or lifecycles out of the box (use custom closures to emulate singletons/scopes if needed).
