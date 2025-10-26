<?php

declare(strict_types=1);

namespace Tests;

use App\Container;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\A;
use Tests\Fixtures\B;

class ContainerTest extends TestCase
{
    public function testManualSetGetUsesResolverEveryTime(): void
    {
        $container = new Container();
        $calls = 0;

        $container->set('thing', function () use (&$calls) {
            $calls++;
            return new \stdClass();
        });

        $first = $container->get('thing');
        $second = $container->get('thing');

        $this->assertInstanceOf(\stdClass::class, $first);
        $this->assertInstanceOf(\stdClass::class, $second);
        $this->assertNotSame($first, $second, 'Resolver should act as factory, returning a new instance each time');
        $this->assertSame(2, $calls, 'Resolver should be called for each get()');
    }

    public function testAutowireResolvesSimpleClass(): void
    {
        $container = new Container();

        // First call should auto-wire and cache the resolver and echo a Setting line once
        $out1 = $this->captureOutput(fn() => $container->get(B::class));
        $this->assertInstanceOf(B::class, $container->get(B::class));

        $this->assertStringContainsString('Setting ' . B::class, $out1);

        // Second call should use cached resolver without additional echo
        $out2 = $this->captureOutput(fn() => $container->get(B::class));
        $this->assertSame('', $out2, 'Subsequent get() should not echo additional Setting lines');
    }

    public function testAutowireInjectsDependencies(): void
    {
        $container = new Container();

        // On first auto-wire of A, it should auto-wire B first, then A
        $out = $this->captureOutput(fn() => $container->get(A::class));
        $this->assertStringContainsString('Setting ' . B::class, $out);
        $this->assertStringContainsString('Setting ' . A::class, $out);

        $a1 = $container->get(A::class);
        $this->assertInstanceOf(A::class, $a1);
        $this->assertInstanceOf(B::class, $a1->getB());

        // Ensure factory behavior: each get returns fresh instances
        $a2 = $container->get(A::class);
        $this->assertNotSame($a1, $a2);
        $this->assertNotSame($a1->getB(), $a2->getB());

        // No new Setting lines on subsequent retrieval
        $out2 = $this->captureOutput(fn() => $container->get(A::class));
        $this->assertSame('', $out2);
    }

    private function captureOutput(callable $fn): string
    {
        \ob_start();
        $fn();
        return (string) \ob_get_clean();
    }
}
