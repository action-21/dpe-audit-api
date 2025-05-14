<?php

namespace App\Database\Opendata\Enveloppe\Porte;

use App\Database\Opendata\Enveloppe\XMLParoiReader;
use App\Domain\Common\ValueObject\{Annee, Id, Orientation, Pourcentage};
use App\Domain\Enveloppe\Enum\{EtatIsolation, TypePose};
use App\Domain\Enveloppe\Enum\Porte\{Materiau, TypeVitrage};
use App\Domain\Enveloppe\ValueObject\Porte\{Menuiserie, Vitrage};

final class XMLPorteReader extends XMLParoiReader
{
    public function supports(): bool
    {
        return $this->nb_porte() > 0 && $this->surface() > 0;
    }

    public function paroi_id(): ?Id
    {
        if (null === $this->reference_paroi()) {
            return null;
        }
        foreach ($this->enveloppe()->parois() as $reader) {
            if ($reader->reference() === $this->reference()) {
                continue;
            }
            if ($reader->reference() === $this->reference_paroi()) {
                return $reader->id();
            }
        }
        return null;
    }

    public function reference_paroi(): ?string
    {
        return $this->findOne('.//reference_paroi')?->reference();
    }

    public function annee_installation(): ?Annee
    {
        return null;
    }

    public function orientation(): ?Orientation
    {
        return null;
    }

    public function isolation(): ?EtatIsolation
    {
        return EtatIsolation::from_enum_type_porte_id($this->enum_type_porte_id());
    }

    public function materiau(): ?Materiau
    {
        return Materiau::from_enum_type_porte_id($this->enum_type_porte_id());
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return TypeVitrage::from_enum_type_porte_id($this->enum_type_porte_id());
    }

    public function taux_vitrage(): Pourcentage
    {
        return match ($this->enum_type_porte_id()) {
            2, 6, 11 => Pourcentage::from(15),
            3, 7, 12 => Pourcentage::from(45),
            4, 8, 10 => Pourcentage::from(30),
            default => Pourcentage::from(0),
        };
    }

    public function presence_sas(): bool
    {
        return $this->enum_type_porte_id() === 14;
    }

    public function type_pose(): TypePose
    {
        return TypePose::from_enum_type_pose_id($this->enum_type_pose_id());
    }

    public function nb_porte(): int
    {
        return $this->findOne('.//nb_porte')?->intval() ?? 1;
    }

    public function surface(): float
    {
        return $this->findOneOrError('.//surface_porte')->floatval() / $this->nb_porte();
    }

    public function largeur_dormant(): ?int
    {
        return $this->findOne('.//largeur_dormant')?->intval() * 10;
    }

    public function presence_retour_isolation(): bool
    {
        return $this->findOne('.//presence_retour_isolation')?->boolval() ?? false;
    }

    public function presence_joint(): bool
    {
        return $this->findOne('.//presence_joint')?->boolval() ?? false;
    }

    public function u(): ?float
    {
        return $this->findOne('.//uporte_saisi')?->floatval();
    }

    public function enum_type_porte_id(): int
    {
        return $this->findOneOrError('.//enum_type_porte_id')->intval();
    }

    public function enum_type_pose_id(): int
    {
        return (int) $this->findOneOrError('.//enum_type_pose_id')->intval();
    }

    public function menuiserie(): Menuiserie
    {
        return Menuiserie::create(
            presence_joint: $this->presence_joint(),
            presence_retour_isolation: $this->presence_retour_isolation(),
            largeur_dormant: $this->largeur_dormant(),
        );
    }

    public function vitrage(): Vitrage
    {
        return Vitrage::create(
            taux_vitrage: $this->taux_vitrage(),
            type_vitrage: $this->type_vitrage(),
        );
    }
}
