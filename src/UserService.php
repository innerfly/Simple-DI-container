<?php

declare(strict_types=1);

namespace App;

readonly class UserService
{
    public function __construct(
        private JsonService $jsonService
    )
    {}

    public function registerUser(string $name): string
    {
        return $this->jsonService->encode([
            'user' => $name,
            'status' => "registered"
        ]);
    }
}
