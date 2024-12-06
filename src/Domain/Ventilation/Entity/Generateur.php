<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\ValueObject\Signaletique;
use App\Domain\Ventilation\Ventilation;
use Webmozart\Assert\Assert;

final class Generateur
{
    public function __construct(
        private readonly Id $id,
        private readonly Ventilation $ventilation,
        private string $description,
        private Signaletique $signaletique,
    ) {}

    public function controle(): void
    {
        Assert::nullOrlessThanEq($this->signaletique->annee_installation, (int) date('Y'));
        Assert::nullOrGreaterThanEq($this->signaletique->annee_installation, $this->ventilation->annee_construction_batiment());
    }

    public function reinitialise(): void {}

    public function id(): Id
    {
        return $this->id;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function signaletique(): Signaletique
    {
        return $this->signaletique;
    }
}
