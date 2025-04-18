<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Data\PontThermiqueData;
use App\Domain\Enveloppe\ValueObject\PontThermique\Liaison;
use Webmozart\Assert\Assert;

final class PontThermique
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private float $longueur,
        private Liaison $liaison,
        private ?float $kpt,
        private PontThermiqueData $data,
    ) {}

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        float $longueur,
        Liaison $liaison,
        ?float $kpt = null,
    ): self {
        Assert::greaterThan($longueur, 0);
        Assert::nullOrGreaterThan($kpt, 0);

        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            longueur: $longueur,
            liaison: $liaison,
            kpt: $kpt,
            data: PontThermiqueData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = PontThermiqueData::create();
        return $this;
    }

    public function calcule(PontThermiqueData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function liaison(): Liaison
    {
        return $this->liaison;
    }

    public function longueur(): float
    {
        return $this->longueur;
    }

    public function kpt(): ?float
    {
        return $this->kpt;
    }

    public function data(): PontThermiqueData
    {
        return $this->data;
    }
}
