<?php

namespace App\Domain\Baie\Entity;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Enum\{SecteurChampsVision, TypeMasqueLointain};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;

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

    public function update(string $description, TypeMasqueLointain $type_masque, float $hauteur, float $orientation,): self
    {
        $this->description = $description;
        $this->type_masque = $type_masque;
        $this->hauteur = $hauteur;
        $this->orientation = $orientation;
        $this->controle();
        return $this;
    }

    public function controle(): void
    {
        Assert::positif($this->hauteur);
        Assert::inferieur_a($this->hauteur, 90);
        Assert::orientation($this->orientation);
        Assert::non_null($this->baie->orientation());

        if ($this->type_masque === TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE) {
            Assert::egal(
                Orientation::from_azimut($this->orientation),
                Orientation::from_azimut($this->baie->orientation())
            );
        }
        if ($this->type_masque === TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE) {
            $borne_inferieure = $this->baie->orientation() - 90;
            $borne_inferieure = $borne_inferieure < 0 ? 360 + $borne_inferieure : $borne_inferieure;
            $borne_superieure = $this->baie->orientation() + 90;
            $borne_superieure = $borne_superieure >= 360 ? $borne_superieure - 360 : $borne_superieure;

            Assert::inferieur_ou_egal_a($borne_inferieure, $this->orientation);
            Assert::superieur_ou_egal_a($borne_superieure, $this->orientation);
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
        $diff = \abs($this->orientation() - $this->baie->orientation());

        return match (Orientation::from_azimut($this->baie->orientation())) {
            Orientation::NORD => match (true) {
                $diff >= -90 && $diff < -45 => SecteurChampsVision::SECTEUR_LATERAL_OUEST,
                $diff >= -45 && $diff <= 0 => SecteurChampsVision::SECTEUR_CENTRAL_OUEST,
                $diff >= 0 && $diff <= 45 => SecteurChampsVision::SECTEUR_CENTRAL_EST,
                $diff > 45 && $diff <= 90 => SecteurChampsVision::SECTEUR_LATERAL_EST,
            },
            Orientation::EST => match (true) {
                $diff >= -90 && $diff < -45 => SecteurChampsVision::SECTEUR_LATERAL_NORD,
                $diff >= -45 && $diff < 0 => SecteurChampsVision::SECTEUR_CENTRAL_NORD,
                $diff >= 0 && $diff <= 45 => SecteurChampsVision::SECTEUR_CENTRAL_SUD,
                $diff > 45 && $diff <= 90 => SecteurChampsVision::SECTEUR_LATERAL_SUD,
            },
            Orientation::SUD => match (true) {
                $diff >= -90 && $diff < -45 => SecteurChampsVision::SECTEUR_LATERAL_EST,
                $diff >= -45 && $diff <= 0 => SecteurChampsVision::SECTEUR_CENTRAL_EST,
                $diff >= 0 && $diff <= 45 => SecteurChampsVision::SECTEUR_CENTRAL_OUEST,
                $diff > 45 && $diff <= 90 => SecteurChampsVision::SECTEUR_LATERAL_OUEST,
            },
            Orientation::OUEST => match (true) {
                $diff >= -90 && $diff < -45 => SecteurChampsVision::SECTEUR_LATERAL_SUD,
                $diff >= -45 && $diff <= 0 => SecteurChampsVision::SECTEUR_CENTRAL_SUD,
                $diff > 0 && $diff <= 45 => SecteurChampsVision::SECTEUR_CENTRAL_NORD,
                $diff > 45 && $diff <= 90 => SecteurChampsVision::SECTEUR_LATERAL_NORD,
            }
        };
    }
}
