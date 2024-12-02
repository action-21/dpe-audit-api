<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\XMLElement;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Entity\{Baie, BaieCollection, Paroi, ParoiCollection};
use App\Domain\Lnc\Enum\{Mitoyennete, TypeBaie, TypeLnc};
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\ValueObject\{Menuiserie, Position};

final class XMLLncTransformer
{
    public function transform(XMLElement $paroi, Enveloppe $enveloppe): ?Lnc
    {
        $reader = XMLLncReader::from($paroi);

        if (false == $reader->apply())
            return null;

        $entity = new Lnc(
            id: $reader->id(),
            enveloppe: $enveloppe,
            description: $reader->description(),
            type: $reader->type_lnc(),
            parois: new ParoiCollection,
            baies: new BaieCollection,
        );

        if ($reader->type_lnc() === TypeLnc::ESPACE_TAMPON_SOLARISE)
            $this->set_baies($reader, $entity);

        $this->set_paroi($reader, $entity);
        $enveloppe->add_local_non_chauffe($entity);
        return $entity;
    }

    private function set_baies(XMLLncReader $reader, Lnc $entity): void
    {
        if (null === $ets_reader = $reader->read_ets())
            throw new \DomainException('Ets not found', 404);

        foreach ($ets_reader->read_baies() as $baie_reader) {
            $entity->add_baie(new Baie(
                id: $baie_reader->id(),
                local_non_chauffe: $entity,
                description: $baie_reader->description(),
                type: $ets_reader->type_baie(),
                surface: $baie_reader->surface_totale(),
                inclinaison: $baie_reader->inclinaison(),
                position: Position::create(
                    orientation: $baie_reader->orientation(),
                    mitoyennete: $baie_reader->mitoyennete(),
                ),
                menuiserie: $ets_reader->type_baie() !== TypeBaie::POLYCARBONATE ? new Menuiserie(
                    nature_menuiserie: $ets_reader->nature_menuiserie(),
                    type_vitrage: $ets_reader->type_vitrage(),
                    presence_rupteur_pont_thermique: $ets_reader->presence_rupteur_pont_thermique(),
                ) : null
            ));
        }
    }

    private function set_paroi(XMLLncReader $reader, Lnc $entity): void
    {
        // Si la somme des surfaces des baies précédemment reconstruites est inférieur à la surface aue de la paroi 
        // alors on ajoute une paroi donnant sur l'extérieur
        if ($entity->baies()->surface() < $reader->surface_aue()) {
            $entity->add_paroi(new Paroi(
                id: Id::create(),
                local_non_chauffe: $entity,
                description: 'Paroi non décrite',
                position: Position::create(
                    orientation: null,
                    mitoyennete: Mitoyennete::EXTERIEUR,
                ),
                surface: $reader->surface_aue(),
                etat_isolation: $reader->isolation_paroi_aue(),
            ));
        }

        // Si la surface aiu est supérieur à la surface de la paroi alors on ajoute une paroi donnant sur un local chauffé
        if ($reader->surface_aiu() > $reader->surface_paroi_totale()) {
            $entity->add_paroi(new Paroi(
                id: Id::create(),
                local_non_chauffe: $entity,
                description: 'Paroi non décrite',
                position: Position::create(
                    orientation: null,
                    mitoyennete: Mitoyennete::LOCAL_RESIDENTIEL,
                ),
                surface: $reader->surface_aiu() - $reader->surface_paroi_totale(),
                etat_isolation: $reader->isolation_paroi_aiu(),
            ));
        }
    }
}
