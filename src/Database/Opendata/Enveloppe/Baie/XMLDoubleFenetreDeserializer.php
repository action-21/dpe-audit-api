<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\Baie\DoubleFenetreData;
use App\Domain\Enveloppe\Entity\Baie\DoubleFenetre;
use App\Domain\Enveloppe\ValueObject\Baie\{Composition, Menuiserie, Performance, Survitrage, Vitrage};

final class XMLDoubleFenetreDeserializer
{
    private XMLDoubleFenetreReader $reader;

    public function deserialize(XMLElement $xml): DoubleFenetre
    {
        $this->reader = XMLDoubleFenetreReader::from($xml);

        return new DoubleFenetre(
            id: $this->reader->id(),
            composition: $this->deserialize_composition(),
            performance: $this->deserialize_performance(),
            data: DoubleFenetreData::create(),
        );
    }

    private function deserialize_composition(): Composition
    {
        return new Composition(
            type_baie: $this->reader->type_baie(),
            type_pose: $this->reader->type_pose(),
            materiau: $this->reader->materiau(),
            presence_soubassement: $this->reader->presence_soubassement(),
            vitrage: $this->deserialize_vitrage(),
            menuiserie: $this->deserialize_menuiserie(),
        );
    }

    private function deserialize_vitrage(): ?Vitrage
    {
        if ($this->reader->type_baie()->is_paroi_vitree()) {
            return null;
        }
        return new Vitrage(
            type_vitrage: $this->reader->type_vitrage(),
            nature_gaz_lame: $this->reader->nature_gaz_lame(),
            epaisseur_lame: $this->reader->epaisseur_lame(),
            survitrage: $this->deserialize_survitrage(),
        );
    }

    private function deserialize_survitrage(): ?Survitrage
    {
        if ($this->reader->type_baie()->is_paroi_vitree()) {
            return null;
        }
        if (null === $this->reader->type_survitrage()) {
            return null;
        }
        return new Survitrage(
            type_survitrage: $this->reader->type_survitrage(),
            epaisseur_lame: $this->reader->epaisseur_survitrage(),
        );
    }

    private function deserialize_menuiserie(): ?Menuiserie
    {
        if ($this->reader->type_baie()->is_paroi_vitree()) {
            return null;
        }
        return new Menuiserie(
            largeur_dormant: $this->reader->largeur_dormant(),
            presence_joint: $this->reader->presence_joint(),
            presence_retour_isolation: $this->reader->presence_retour_isolation(),
            presence_rupteur_pont_thermique: $this->reader->presence_rupteur_pont_thermique(),
        );
    }

    private function deserialize_performance(): Performance
    {
        return new Performance(
            ug: $this->reader->ug_saisi(),
            uw: $this->reader->uw_saisi(),
            sw: $this->reader->sw_saisi(),
            ujn: null,
        );
    }
}
