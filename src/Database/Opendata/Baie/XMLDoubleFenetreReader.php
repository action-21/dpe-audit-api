<?php

namespace App\Database\Opendata\Baie;

use App\Database\Opendata\XMLReader;
use App\Domain\Baie\Enum\{NatureGazLame, NatureMenuiserie, TypeBaie, TypePose, TypeSurvitrage, TypeVitrage};
use App\Domain\Baie\ValueObject\{Menuiserie, Survitrage};

final class XMLDoubleFenetreReader extends XMLReader
{
    public function inclinaison(): float
    {
        return $this->xml()->findOneOrError('.//enum_inclinaison_vitrage_id')->inclinaison();
    }

    public function menuiserie(): ?Menuiserie
    {
        return $this->nature_menuiserie() && $this->type_vitrage() ? new Menuiserie(
            nature: $this->nature_menuiserie(),
            type_vitrage: $this->type_vitrage(),
            type_pose: $this->type_pose(),
            presence_joint: false,
            presence_retour_isolation: false,
            largeur_dormant: null,
            survitrage: $this->survitrage(),
            presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique(),
            nature_gaz_lame: $this->nature_gaz_lame(),
            epaisseur_lame: $this->epaisseur_lame(),
        ) : null;
    }

    public function survitrage(): ?Survitrage
    {
        return $this->type_survitrage() ? new Survitrage(
            type_survitrage: $this->type_survitrage(),
            epaisseur_lame: $this->epaisseur_lame(),
        ) : null;
    }

    public function type_baie(): TypeBaie
    {
        return TypeBaie::from_enum_type_baie_id($this->enum_type_baie_id());
    }

    public function type_pose(): TypePose
    {
        return TypePose::from_enum_type_pose_id($this->enum_type_pose_id());
    }

    public function nature_menuiserie(): ?NatureMenuiserie
    {
        return NatureMenuiserie::from_enum_type_materiaux_menuiserie_id($this->enum_type_materiaux_menuiserie_id());
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

    public function nature_gaz_lame(): ?NatureGazLame
    {
        return ($value = $this->enum_type_gaz_lame_id()) ? NatureGazLame::from_enum_type_gaz_lame_id($value) : null;
    }

    public function epaisseur_lame(): ?int
    {
        return $this->xml()->findOne('.//epaisseur_lame')?->intval();
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

    public function ug_saisi(): ?float
    {
        return $this->xml()->findOne('.//ug_saisi')?->floatval();
    }

    public function uw_saisi(): ?float
    {
        return $this->xml()->findOne('.//uw_saisi')?->floatval();
    }

    public function sw_saisi(): ?float
    {
        return $this->xml()->findOne('.//sw_saisi')?->floatval();
    }

    public function enum_type_baie_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_baie_id')->intval();
    }

    public function enum_type_materiaux_menuiserie_id(): int
    {
        return $this->xml()->findOne('.//enum_type_materiaux_menuiserie_id')->intval();
    }

    public function enum_type_vitrage_id(): int
    {
        return $this->xml()->findOne('.//enum_type_vitrage_id')->intval();
    }

    public function enum_type_gaz_lame_id(): ?int
    {
        return $this->xml()->findOne('.//enum_type_gaz_lame_id')?->intval();
    }

    public function enum_type_pose_id(): int
    {
        return $this->xml()->findOne('.//enum_type_pose_id')->intval();
    }

    public function vitrage_vir(): bool
    {
        return $this->xml()->findOne('.//vitrage_vir')?->boolval() ?? false;
    }

    // Données intermédiaires

    public function uw(): float
    {
        return $this->xml()->findOneOrError('.//uw')->floatval();
    }

    public function sw(): float
    {
        return $this->xml()->findOneOrError('.//sw')->floatval();
    }
}
