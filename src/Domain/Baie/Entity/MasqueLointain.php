<?php

namespace App\Domain\Baie\Entity;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Enum\{SecteurChampsVision, TypeMasqueLointain};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class MasqueLointain
{
    public function __construct(
        private readonly Id $id,
        private readonly Baie $baie,
        private string $description,
        private TypeMasqueLointain $type_masque,
        private float $hauteur,
        private float $orientation,
    ) {}

    public static function create(
        Baie $baie,
        string $description,
        TypeMasqueLointain $type_masque,
        float $hauteur,
        float $orientation,
    ): self {
        $entity = new self(
            id: Id::create(),
            baie: $baie,
            description: $description,
            type_masque: $type_masque,
            hauteur: $hauteur,
            orientation: $orientation,
        );
        $entity->controle();
        return $entity;
    }

    public function controle(): void
    {
        Assert::greaterThan($this->hauteur, 0);
        Assert::lessThan($this->hauteur, 90);
        Assert::greaterThanEq($this->orientation, 0);
        Assert::lessThan($this->orientation, 360);
        Assert::notNull($this->baie->orientation());

        if ($this->type_masque === TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE) {
            Assert::same(
                Orientation::from_azimut($this->orientation)->value,
                Orientation::from_azimut($this->baie->orientation())->value,
            );
        }
        if ($this->type_masque === TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE) {
            $borne_inferieure = $this->baie->orientation() - 90;
            $borne_inferieure = $borne_inferieure < 0 ? 360 + $borne_inferieure : $borne_inferieure;
            $borne_superieure = $this->baie->orientation() + 90;
            $borne_superieure = $borne_superieure >= 360 ? $borne_superieure - 360 : $borne_superieure;

            Assert::lessThanEq($this->orientation, $borne_inferieure);
            Assert::greaterThanEq($this->orientation, $borne_superieure);
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

    public function type_masque(): TypeMasqueLointain
    {
        return $this->type_masque;
    }

    public function hauteur(): float
    {
        return $this->hauteur;
    }

    public function orientation(): float
    {
        return $this->orientation;
    }

    public function secteur(): SecteurChampsVision
    {
        return SecteurChampsVision::determine($this->orientation, $this->baie->orientation());
    }
}
