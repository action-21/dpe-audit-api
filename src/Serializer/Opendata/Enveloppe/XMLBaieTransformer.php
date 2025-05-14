<?php

namespace App\Serializer\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\Baie\XMLBaieReader;
use App\Domain\Enveloppe\Entity\Baie;
use App\Domain\Enveloppe\Entity\Baie\{DoubleFenetre, MasqueLointain, MasqueProche};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Baie\Position;
use Webmozart\Assert\Assert;

final class XMLBaieTransformer
{
    private XMLBaieReader $reader;
    private Baie $entity;

    public function to(XMLBaieReader $reader, Enveloppe $entity): Baie
    {
        $this->reader = $reader;

        $paroi = null;
        $local_non_chauffe = null;

        if ($reader->paroi_id()) {
            $paroi = $entity->paroi($reader->paroi_id());
            Assert::notNull($paroi, "Paroi {$reader->paroi_id()} non trouvée");
        }
        if ($reader->local_non_chauffe_id()) {
            $local_non_chauffe = $entity->locaux_non_chauffes()->find($reader->local_non_chauffe_id());
            Assert::notNull($local_non_chauffe, "Local non chauffé {$reader->local_non_chauffe_id()} non trouvé");
        }
        $this->entity = Baie::create(
            id: $reader->id(),
            enveloppe: $entity,
            description: $reader->description(),
            presence_protection_solaire: $reader->presence_protection_solaire(),
            type_fermeture: $reader->type_fermeture(),
            annee_installation: $reader->annee_installation(),
            composition: $reader->composition(),
            performance: $reader->performance(),
            position: Position::create(
                surface: $reader->surface(),
                mitoyennete: $reader->mitoyennete(),
                inclinaison: $reader->inclinaison(),
                orientation: $reader->orientation(),
                paroi: $paroi,
                local_non_chauffe: $local_non_chauffe,
            ),
            double_fenetre: $this->deserialize_double_fenetre(),
        );

        $this->deserialize_masques_lointains();
        $this->deserialize_masques_proches();

        return $this->entity;
    }

    private function deserialize_double_fenetre(): ?DoubleFenetre
    {
        if (null === $reader = $this->reader->double_fenetre()) {
            return null;
        }
        return DoubleFenetre::create(
            id: $reader->id(),
            composition: $reader->composition(),
            performance: $reader->performance(),
        );
    }

    private function deserialize_masques_lointains(): void
    {
        foreach ($this->reader->masques_lointains() as $reader) {
            $this->entity->add_masque_lointain(MasqueLointain::create(
                id: $this->reader->id(),
                baie: $this->entity,
                description: $reader->description(),
                type_masque: $reader->type_masque(),
                hauteur: $reader->hauteur(),
                orientation: $reader->orientation(),
            ));
        }
    }

    private function deserialize_masques_proches(): void
    {
        foreach ($this->reader->masques_proches() as $reader) {
            $this->entity->add_masque_proche(MasqueProche::create(
                id: $reader->id(),
                baie: $this->entity,
                description: $reader->description(),
                type_masque: $reader->type_masque(),
                profondeur: $reader->profondeur(),
            ));
        }
    }
}
