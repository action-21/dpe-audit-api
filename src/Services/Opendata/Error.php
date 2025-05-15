<?php

namespace App\Services\Opendata;

final class Error implements \Stringable
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
    ) {}

    public function __toString(): string
    {
        return sprintf('Error %d: %s', $this->code, $this->message);
    }
}
