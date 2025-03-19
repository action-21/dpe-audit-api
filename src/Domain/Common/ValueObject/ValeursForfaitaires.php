<?php

namespace App\Domain\Common\ValueObject;

final class ValeursForfaitaires
{
    public function __construct(
        /** @var string[] */
        private array $valeurs = [],
    ) {}

    public static function create(): self
    {
        return new self([]);
    }

    public function add(string $key): self
    {
        if (false === \in_array($key, $this->valeurs, true)) {
            $this->valeurs[] = $key;
        }
        return $this;
    }

    public function count(): int
    {
        return count($this->valeurs);
    }

    public function reset(): self
    {
        return $this->create();
    }

    public function get(): array
    {
        return $this->valeurs;
    }
}
