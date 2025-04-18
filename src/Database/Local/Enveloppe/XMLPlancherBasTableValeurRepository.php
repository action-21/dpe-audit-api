<?php

namespace App\Database\Local\Enveloppe;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Functions;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Domain\Enveloppe\Enum\PlancherBas\TypePlancherBas;
use App\Domain\Enveloppe\Service\PlancherBasTableValeurRepository;

final class XMLPlancherBasTableValeurRepository extends XMLParoiTableValeurRepository implements PlancherBasTableValeurRepository
{
    public function u0(?TypePlancherBas $type_structure): ?float
    {
        return $this->db->repository('plancher_bas.u0')
            ->createQuery()
            ->and('type_structure', $type_structure)
            ->getOne()
            ?->floatval('u0');
    }

    public function u(
        ZoneClimatique $zone_climatique,
        Annee $annee_construction_isolation,
        bool $effet_joule,
    ): ?float {
        return $this->db->repository('plancher_bas.u')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('effet_joule', $effet_joule)
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation->value())
            ->getOne()
            ?->floatval('u');
    }

    public function ue(
        Mitoyennete $mitoyennete,
        Annee $annee_construction,
        float $perimetre,
        float $surface,
        float $u,
    ): ?float {
        $_2sp = \round(2 * $surface / $perimetre);

        $records = $this->db->repository('plancher_bas.ue')
            ->createQuery()
            ->and('mitoyennete', $mitoyennete)
            ->andCompareTo('annee_construction', $annee_construction->value())
            ->getMany()
            ->usort(name: '_2sp', value: $_2sp)
            ->slice(0, 2);

        if (0 === $records->count()) {
            return null;
        }
        if (1 === $records->count()) {
            return $records->first()->floatval('ue');
        }
        return Functions::interpolation_lineaire(
            x: $u,
            x1: $records->first()->floatval('u'),
            x2: $records->last()->floatval('u'),
            y1: $records->first()->floatval('ue'),
            y2: $records->last()->floatval('ue'),
        );
    }
}
