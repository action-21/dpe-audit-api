<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Service\Assert;

final class Solaire
{
    public function __construct(public readonly ?float $fch,) {}

    public static function create(?float $fch): self
    {
        return new self(fch: $fch,);
    }

    public function controle(): void
    {
        Assert::positif_ou_zero($this->fch);
        Assert::inferieur_ou_egal_a($this->fch, 1);
    }
}
