<?php

namespace App\Database\Local\Enveloppe;

use App\Database\Local\XMLTableElement;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Functions;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\Mur\TypeMur;
use App\Domain\Enveloppe\Service\MurTableValeurRepository;

final class XMLMurTableValeurRepository extends XMLParoiTableValeurRepository implements MurTableValeurRepository
{
    public function u0(
        Annee $annee_construction,
        ?TypeMur $type_structure,
        ?float $epaisseur_structure,
    ): ?float {
        $records = $this->db->repository('mur.u0')
            ->createQuery()
            ->and('type_structure', $type_structure)
            ->andCompareTo('annee_construction', $annee_construction->value())
            ->getMany();

        if ($record = $records->find('epaisseur_structure', $epaisseur_structure)) {
            return $record->floatval('u0');
        }

        $records = $records->usort(name: 'epaisseur_structure', value: $epaisseur_structure)->slice(0, 2);

        if (0 === $records->count()) {
            return null;
        }
        if (1 === $records->count()) {
            return $records->first()->floatval('u0');
        }
        return Functions::interpolation_lineaire(
            x: $epaisseur_structure,
            x1: $records->first()->floatval('epaisseur_structure'),
            x2: $records->last()->floatval('epaisseur_structure'),
            y1: $records->first()->floatval('u0'),
            y2: $records->last()->floatval('u0'),
        );
    }

    public function u(
        ZoneClimatique $zone_climatique,
        Annee $annee_construction_isolation,
        bool $effet_joule,
    ): ?float {
        return $this->db->repository('mur.u')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('effet_joule', $effet_joule)
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation->value())
            ->getOne()
            ?->floatval('u');
    }
}
