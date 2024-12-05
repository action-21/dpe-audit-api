<?php

namespace App\Domain\Baie\Entity;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Enum\TypeMasqueProche;
use App\Domain\Common\Type\Id;
use Webmozart\Assert\Assert;

final class MasqueProche
{
    public function __construct(
        private readonly Id $id,
        private readonly Baie $baie,
        private string $description,
        private TypeMasqueProche $type_masque,
        private ?float $avancee = null,
    ) {}

    public function set_balcon_ou_auvent(TypeMasqueProche\BalconAuvent $type, float $avancee): self
    {
        $this->type_masque = $type->to();
        $this->avancee = $avancee;
        $this->controle();
        return $this;
    }

    public function set_paroi_laterale(TypeMasqueProche\ParoiLaterale $type): self
    {
        $this->type_masque = $type->to();
        $this->avancee = null;
        $this->controle();
        return $this;
    }

    public function update(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function controle(): void
    {
        Assert::greaterThanEq($this->avancee, 0);
        if ($this->type_masque === TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS) {
            Assert::notNull($this->baie->orientation());
        }
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function baie(): Baie
    {
        return $this->baie;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_masque(): TypeMasqueProche
    {
        return $this->type_masque;
    }

    public function avancee(): ?float
    {
        return $this->avancee;
    }
}
