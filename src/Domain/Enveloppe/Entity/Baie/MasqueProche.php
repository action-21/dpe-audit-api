<?php

namespace App\Domain\Enveloppe\Entity\Baie;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Data\Baie\MasqueProcheData;
use App\Domain\Enveloppe\Entity\Baie;
use App\Domain\Enveloppe\Enum\Baie\TypeMasqueProche;
use Webmozart\Assert\Assert;

final class MasqueProche
{
    public function __construct(
        private readonly Id $id,
        private readonly Baie $baie,
        private string $description,
        private TypeMasqueProche $type_masque,
        private ?float $profondeur,
        private MasqueProcheData $data,
    ) {}

    public static function create(
        Id $id,
        Baie $baie,
        string $description,
        TypeMasqueProche $type_masque,
        ?float $profondeur,
    ): self {
        Assert::nullOrGreaterThanEq($profondeur, 0);

        return new self(
            id: $id,
            baie: $baie,
            description: $description,
            type_masque: $type_masque,
            profondeur: $profondeur,
            data: MasqueProcheData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = MasqueProcheData::create();
        return $this;
    }

    public function calcule(MasqueProcheData $data): self
    {
        $this->data = $data;
        return $this;
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

    public function profondeur(): ?float
    {
        return $this->profondeur;
    }

    public function data(): MasqueProcheData
    {
        return $this->data;
    }
}
