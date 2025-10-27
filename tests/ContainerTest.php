<?php

declare(strict_types=1);

namespace Tests;

use App\Container;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\A;
use Tests\Fixtures\B;

class ContainerTest extends TestCase
{

    public function testAutoWiringResolves(): void
    {
        $container = new Container();

        $a1 = $container->get(A::class);
        $a2 = $container->get(A::class);

        $this->assertInstanceOf(A::class, $a1);
        $this->assertInstanceOf(B::class, $a1->getB());

        $this->assertFalse($a1 === $a2);
        $this->assertTrue($a1->getB() === $a2->getB());;
    }
}
