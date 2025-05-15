<?php

namespace App\Services\Opendata;

/**
 * @property array<array> $results
 */
final class Lines
{
    public function __construct(
        public readonly int $total,
        public readonly string $next,
        public readonly array $results,
    ) {}
}
