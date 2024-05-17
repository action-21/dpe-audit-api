<?php

namespace App\Database\Opendata\Baie;

use App\Database\Opendata\XMLElement;
use App\Domain\Baie\Enum\{InclinaisonVitrage as EnumInclinaisonVitrage, NatureGazLame, NatureMenuiserie, TypeBaie, TypePose, TypeVitrage};
use App\Domain\Baie\ValueObject\{DoubleFenetre, EpaisseurLameAir, InclinaisonVitrage, Sw, Ug, Uw};
use App\Domain\Common\Identifier\Uuid;

final class XMLDoubleFenetreReader
{
    private XMLElement $xml;

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function description(): string
    {
        return $this->xml->findOne('.//description')?->getValue() ?? "Double fenêtre non décrite";
    }

    public function enum_type_baie_id(): int
    {
        return (int) $this->xml->findOneOrError('.//enum_type_baie_id')->getValue();
    }

    public function enum_type_baie(): TypeBaie
    {
        return TypeBaie::from_enum_type_baie_id($this->enum_type_baie_id());
    }

    public function enum_type_pose_id(): int
    {
        return (int) $this->xml->findOneOrError('.//enum_type_pose_id')->getValue();
    }

    public function enum_type_pose(): TypePose
    {
        return TypePose::from($this->enum_type_pose_id());
    }

    public function enum_type_materiaux_menuiserie_id(): int
    {
        return (int) $this->xml->findOneOrError('.//enum_type_materiaux_menuiserie_id')->getValue();
    }

    public function enum_type_materiaux_menuiserie(): NatureMenuiserie
    {
        return NatureMenuiserie::from_enum_type_materiaux_menuiserie_id($this->enum_type_materiaux_menuiserie_id());
    }

    public function enum_type_vitrage_id(): int
    {
        return (int) $this->xml->findOneOrError('.//enum_type_vitrage_id')->getValue();
    }

    public function enum_type_vitrage(): TypeVitrage
    {
        return TypeVitrage::try_from_opendata($this->enum_type_vitrage_id(), $this->vitrage_vir());
    }

    public function enum_inclinaison_vitrage_id(): int
    {
        return (int) $this->xml->findOneOrError('.//enum_inclinaison_vitrage_id')->getValue();
    }

    public function enum_inclinaison_vitrage(): EnumInclinaisonVitrage
    {
        return EnumInclinaisonVitrage::from_enum_inclinaison_vitrage_id($this->enum_inclinaison_vitrage_id());
    }

    public function enum_type_gaz_lame_id(): ?int
    {
        return (null !== $value = $this->xml->findOne('.//enum_type_gaz_lame_id')?->getValue()) ? (int) $value : null;
    }

    public function enum_type_gaz_lame(): ?NatureGazLame
    {
        return ($value = $this->enum_type_gaz_lame_id()) ? NatureGazLame::from_enum_type_gaz_lame_id($value) : null;
    }

    public function epaisseur_lame(): ?EpaisseurLameAir
    {
        return ($value = $this->xml->findOne('.//epaisseur_lame')?->getValue()) ? EpaisseurLameAir::from($value) : null;
    }

    public function vitrage_vir(): ?bool
    {
        return (null !== $value = $this->xml->findOne('.//vitrage_vir')?->getValue()) ? (bool) $value : null;
    }

    public function ug_saisi(): ?Ug
    {
        return ($value = $this->xml->findOne('.//ug_saisi')?->getValue()) ? Ug::from($value) : null;
    }

    public function uw_saisi(): ?Uw
    {
        return ($value = $this->xml->findOne('.//uw_saisi')?->getValue()) ? Uw::from($value) : null;
    }

    public function sw_saisi(): ?Sw
    {
        return ($value = $this->xml->findOne('.//sw_saisi')?->getValue()) ? Sw::from($value) : null;
    }

    // Données déduites

    public function inclinaison_vitrage(): InclinaisonVitrage
    {
        return InclinaisonVitrage::from($this->enum_inclinaison_vitrage()->to_int());
    }

    public function double_fenetre(): DoubleFenetre
    {
        return new DoubleFenetre(
            type_baie: $this->enum_type_baie(),
            type_pose: $this->enum_type_pose(),
            nature_menuiserie: $this->enum_type_materiaux_menuiserie(),
            type_vitrage: $this->enum_type_vitrage(),
            inclinaison_vitrage: $this->inclinaison_vitrage(),
            epaisseur_lame: $this->epaisseur_lame(),
            nature_gaz_lame: $this->enum_type_gaz_lame(),
            ug: $this->ug_saisi(),
            uw: $this->uw_saisi(),
            sw: $this->sw_saisi(),
        );
    }

    public function read(XMLElement $xml): self
    {
        $this->xml = $xml;
        return $this;
    }
}
