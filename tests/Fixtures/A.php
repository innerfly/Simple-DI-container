<?php

declare(strict_types=1);

namespace Tests\Fixtures;

class A
{
    public function __construct(private B $b)
    {
    }

    public function getB(): B
    {
        return $this->b;
    }
}
