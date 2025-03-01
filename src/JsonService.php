<?php

declare(strict_types=1);

namespace App;

readonly class JsonService {
    public function encode(array $data ): string
    {
        return json_encode($data);
    }
}
