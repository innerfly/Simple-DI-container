<?php

declare(strict_types=1);

namespace Tests;

use App\Container;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\A;
use Tests\Fixtures\B;

class ContainerTest extends TestCase
{

    public function testAutoWiringResolvesDependencies(): void
    {
        $container = new Container();

        $a = $container->get(A::class);

        $this->assertInstanceOf(A::class, $a);
        $this->assertInstanceOf(B::class, $a->getB());
    }

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
        $this->assertSame(2, $calls, 'Resolver should be called for each get()');
    }

    public function testGetUnknownClassThrowsReflectionException(): void
    {
        $container = new Container();

        $this->expectException(\ReflectionException::class);
        $container->get('This\\Class\\DoesNotExist_12345');
    }
}
