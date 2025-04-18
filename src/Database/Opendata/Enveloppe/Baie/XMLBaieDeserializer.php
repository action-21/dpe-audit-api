<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\BaieData;
use App\Domain\Enveloppe\Entity\Baie;
use App\Domain\Enveloppe\Entity\Baie\{DoubleFenetre, MasqueProcheCollection, MasqueLointainCollection};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Baie\{Composition, Menuiserie, Performance, Position, Survitrage, Vitrage};
use Webmozart\Assert\Assert;

final class XMLBaieDeserializer
{
    private XMLBaieReader $reader;
    private Baie $entity;

    public function __construct(
        private readonly XMLDoubleFenetreDeserializer $double_fenetre_deserializer,
        private readonly XMLMasqueProcheDeserializer $masque_proche_deserializer,
        private readonly XMLMasqueLointainDeserializer $masque_lointain_deserializer,
    ) {}

    public function deserialize(XMLElement $xml, Enveloppe $entity): Baie
    {
        $this->reader = XMLBaieReader::from($xml);

        $paroi = null;
        $local_non_chauffe = null;

        if ($this->reader->paroi_id()) {
            $paroi = $entity->paroi($this->reader->paroi_id());
            Assert::notNull($paroi, "Paroi {$this->reader->paroi_id()} non trouvée");
        }
        if ($this->reader->local_non_chauffe_id()) {
            $local_non_chauffe = $entity->locaux_non_chauffes()->find($this->reader->local_non_chauffe_id());
            Assert::notNull($local_non_chauffe, "Local non chauffé {$this->reader->local_non_chauffe_id()} non trouvé");
        }

        $baie_entity = new Baie(
            id: $this->reader->id(),
            enveloppe: $entity,
            description: $this->reader->description(),
            presence_protection_solaire: $this->reader->presence_protection_solaire(),
            type_fermeture: $this->reader->type_fermeture(),
            annee_installation: $this->reader->annee_installation(),
            composition: $this->deserialize_composition(),
            performance: $this->deserialize_performance(),
            position: new Position(
                surface: $this->reader->surface(),
                mitoyennete: $this->reader->mitoyennete(),
                inclinaison: $this->reader->inclinaison(),
                orientation: $this->reader->orientation(),
                paroi: $paroi,
                local_non_chauffe: $local_non_chauffe,
            ),
            double_fenetre: $this->deserialize_double_fenetre(),
            masques_proches: new MasqueProcheCollection,
            masques_lointains: new MasqueLointainCollection,
            data: BaieData::create(),
        );

        foreach ($this->reader->masques_proches() as $reader) {
            $baie_entity->add_masque_proche(
                $this->masque_proche_deserializer->deserialize(
                    xml: $reader->xml(),
                    entity: $baie_entity,
                )
            );
        }

        foreach ($this->reader->masques_lointains() as $reader) {
            $baie_entity->add_masque_lointain(
                $this->masque_lointain_deserializer->deserialize(
                    xml: $reader->xml(),
                    entity: $baie_entity,
                )
            );
        }

        return $baie_entity;
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
            ujn: $this->reader->ujn_saisi(),
            sw: $this->reader->sw_saisi(),
        );
    }

    private function deserialize_double_fenetre(): ?DoubleFenetre
    {
        if (null === $reader = $this->reader->double_fenetre()) {
            return null;
        }
        return $this->double_fenetre_deserializer->deserialize($reader->xml(), $this->entity);
    }
}
