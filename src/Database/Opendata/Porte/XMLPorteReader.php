<?php

namespace App\Database\Opendata\Porte;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Porte\Enum\{EtatIsolation, Mitoyennete, NatureMenuiserie, TypePose, TypeVitrage};
use App\Domain\Porte\ValueObject\{Caracteristique, Menuiserie, Position, Vitrage};

final class XMLPorteReader extends XMLReader
{
    public function match(string $reference): bool
    {
        return $reference === $this->reference() || $reference === $this->xml()->findOne('.//description')?->reference();
    }

    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->reference();
    }

    public function paroi_id(): ?Id
    {
        return $this->xml()->findOne('.//reference_paroi')?->id();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? "Porte";
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->enum_cfg_isolation_lnc_id() === 1
            ? Mitoyennete::LOCAL_NON_ACCESSIBLE
            : Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function position(?Id $paroi_id, ?Id $local_non_chauffe_id,): Position
    {
        return new Position(
            paroi_id: $paroi_id,
            local_non_chauffe_id: $local_non_chauffe_id,
            mitoyennete: $this->mitoyennete(),
        );
    }

    public function caracteristique(): Caracteristique
    {
        return new Caracteristique(
            surface: $this->surface(),
            presence_sas: $this->presence_sas(),
            isolation: $this->isolation(),
            menuiserie: $this->menuiserie(),
            vitrage: $this->vitrage(),
            annee_installation: null,
            u: $this->uporte(),
        );
    }

    public function menuiserie(): Menuiserie
    {
        return new Menuiserie(
            nature_menuiserie: $this->nature_menuiserie(),
            type_pose: $this->type_pose(),
            largeur_dormant: $this->largeur_dormant(),
            presence_joint: $this->presence_joint(),
            presence_retour_isolation: $this->presence_retour_isolation(),
        );
    }

    public function vitrage(): Vitrage
    {
        return new Vitrage(
            taux_vitrage: $this->taux_vitrage(),
            type_vitrage: $this->type_vitrage(),
        );
    }

    public function isolation(): EtatIsolation
    {
        return EtatIsolation::from_enum_type_porte_id($this->enum_type_porte_id());
    }

    public function nature_menuiserie(): NatureMenuiserie
    {
        return NatureMenuiserie::from_enum_type_porte_id($this->enum_type_porte_id());
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return TypeVitrage::from_enum_type_porte_id($this->enum_type_porte_id());
    }

    public function taux_vitrage(): float
    {
        return match ($this->enum_type_porte_id()) {
            2, 6, 11 => 15,
            3, 7, 12 => 45,
            4, 8, 10 => 30,
            default => 0,
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

    public function quantite(): int
    {
        return $this->xml()->findOne('.//nb_porte')?->intval() ?? 1;
    }

    public function surface(): float
    {
        return $this->xml()->findOneOrError('.//surface_porte')->floatval() / $this->quantite();
    }

    public function largeur_dormant(): ?int
    {
        return $this->xml()->findOne('.//largeur_dormant')?->intval() * 10;
    }

    public function presence_retour_isolation(): bool
    {
        return $this->xml()->findOne('.//presence_retour_isolation')?->boolval() ?? false;
    }

    public function presence_joint(): bool
    {
        return $this->xml()->findOne('.//presence_joint')?->boolval() ?? false;
    }

    public function u_saisi(): ?float
    {
        return $this->xml()->findOne('.//uporte_saisi')?->floatval();
    }

    public function enum_type_adjacence_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_adjacence_id')->intval();
    }

    public function enum_cfg_isolation_lnc_id(): ?int
    {
        return $this->xml()->findOne('.//enum_cfg_isolation_lnc_id')?->intval();
    }

    public function enum_type_porte_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_porte_id')->intval();
    }

    public function enum_type_pose_id(): int
    {
        return (int) $this->xml()->findOneOrError('.//enum_type_pose_id')->intval();
    }

    // Données intermédiaires

    public function uporte(): float
    {
        return $this->xml()->findOneOrError('.//uporte')->floatval();
    }

    public function b(): float
    {
        return $this->xml()->findOneOrError('.//b')->floatval();
    }
}
