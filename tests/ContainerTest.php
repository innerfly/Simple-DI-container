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
        $this->assertSame(2, $calls, 'Resolver should be called for each get()');
    }
}
