<?php

namespace App\Domain\Enveloppe\Entity\Baie;

use App\Domain\Common\ValueObject\{Id, Orientation};
use App\Domain\Enveloppe\Data\Baie\MasqueLointainData;
use App\Domain\Enveloppe\Entity\Baie;
use App\Domain\Enveloppe\Enum\Baie\TypeMasqueLointain;
use Webmozart\Assert\Assert;

final class MasqueLointain
{
    public function __construct(
        private readonly Id $id,
        private readonly Baie $baie,
        private string $description,
        private TypeMasqueLointain $type_masque,
        private float $hauteur,
        private Orientation $orientation,
        private MasqueLointainData $data,
    ) {}

    public static function create(
        Id $id,
        Baie $baie,
        string $description,
        TypeMasqueLointain $type_masque,
        float $hauteur,
        Orientation $orientation,
    ): self {
        Assert::greaterThan($hauteur, 0);
        Assert::lessThan($hauteur, 90);

        return new self(
            id: $id,
            baie: $baie,
            description: $description,
            type_masque: $type_masque,
            hauteur: $hauteur,
            orientation: $orientation,
            data: MasqueLointainData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = MasqueLointainData::create();
        return $this;
    }

    public function calcule(MasqueLointainData $data): self
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

    public function type_masque(): TypeMasqueLointain
    {
        return $this->type_masque;
    }

    public function hauteur(): float
    {
        return $this->hauteur;
    }

    public function orientation(): Orientation
    {
        return $this->orientation;
    }

    public function data(): MasqueLointainData
    {
        return $this->data;
    }
}
