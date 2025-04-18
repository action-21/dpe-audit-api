<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\{Id, Inclinaison};
use App\Domain\Enveloppe\Enum\Baie\{Materiau, NatureGazLame, TypeBaie, TypeSurvitrage, TypeVitrage};
use App\Domain\Enveloppe\Enum\TypePose;

final class XMLDoubleFenetreReader extends XMLReader
{
    public function baie(): XMLBaieReader
    {
        return XMLBaieReader::from($this->findOneOrError('//ancestor::baie_vitree'));
    }

    public function id(): Id
    {
        return $this->baie()->id();
    }

    public function inclinaison(): Inclinaison
    {
        return Inclinaison::from_enum_inclinaison_vitrage_id(
            $this->findOneOrError('.//enum_inclinaison_vitrage_id')->intval()
        );
    }

    public function type_baie(): TypeBaie
    {
        return TypeBaie::from_enum_type_baie_id($this->enum_type_baie_id());
    }

    public function type_pose(): TypePose
    {
        return TypePose::from_enum_type_pose_id($this->enum_type_pose_id());
    }

    public function materiau(): ?Materiau
    {
        return Materiau::from_enum_type_materiaux_menuiserie_id($this->enum_type_materiaux_menuiserie_id());
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return TypeVitrage::from_enum_type_vitrage_id(
            id: $this->enum_type_vitrage_id(),
            vitrage_vir: $this->vitrage_vir(),
        );
    }

    public function type_survitrage(): ?TypeSurvitrage
    {
        return TypeSurvitrage::from_enum_type_vitrage_id(
            id: $this->enum_type_vitrage_id(),
            vitrage_vir: $this->vitrage_vir(),
        );
    }

    public function epaisseur_survitrage(): ?float
    {
        return null;
    }

    public function nature_gaz_lame(): ?NatureGazLame
    {
        return ($value = $this->enum_type_gaz_lame_id()) ? NatureGazLame::from_enum_type_gaz_lame_id($value) : null;
    }

    public function epaisseur_lame(): ?int
    {
        return $this->findOne('.//epaisseur_lame')?->intval();
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->enum_type_materiaux_menuiserie_id() === 6 ? true : false;
    }

    public function presence_soubassement(): ?bool
    {
        return match ($this->enum_type_baie_id()) {
            7 => false,
            8 => true,
            default => null,
        };
    }

    public function largeur_dormant(): ?float
    {
        return null;
    }

    public function presence_joint(): ?bool
    {
        return null;
    }

    public function presence_retour_isolation(): ?bool
    {
        return null;
    }

    public function ug_saisi(): ?float
    {
        return $this->findOne('.//ug_saisi')?->floatval();
    }

    public function uw_saisi(): ?float
    {
        return $this->findOne('.//uw_saisi')?->floatval();
    }

    public function sw_saisi(): ?float
    {
        return $this->findOne('.//sw_saisi')?->floatval();
    }

    public function enum_type_baie_id(): int
    {
        return $this->findOneOrError('.//enum_type_baie_id')->intval();
    }

    public function enum_type_materiaux_menuiserie_id(): int
    {
        return $this->findOne('.//enum_type_materiaux_menuiserie_id')->intval();
    }

    public function enum_type_vitrage_id(): int
    {
        return $this->findOne('.//enum_type_vitrage_id')->intval();
    }

    public function enum_type_gaz_lame_id(): ?int
    {
        return $this->findOne('.//enum_type_gaz_lame_id')?->intval();
    }

    public function enum_type_pose_id(): int
    {
        return $this->findOne('.//enum_type_pose_id')->intval();
    }

    public function vitrage_vir(): bool
    {
        return $this->findOne('.//vitrage_vir')?->boolval() ?? false;
    }

    // Données intermédiaires

    public function uw(): float
    {
        return $this->findOneOrError('.//uw')->floatval();
    }

    public function sw(): float
    {
        return $this->findOneOrError('.//sw')->floatval();
    }
}
