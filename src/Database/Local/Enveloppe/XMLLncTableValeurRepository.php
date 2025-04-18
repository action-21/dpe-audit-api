<?php

namespace App\Database\Local\Enveloppe;

use App\Domain\Common\Enum\{Mois, Orientation, ZoneClimatique};
use App\Domain\Common\ValueObject\Inclinaison;
use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Enum\Lnc\{Materiau, TypeBaie, TypeLnc, TypeVitrage};
use App\Domain\Enveloppe\Service\LncTableValeurRepository;
use App\Database\Local\XMLTableDatabase;

final class XMLLncTableValeurRepository implements LncTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function uvue(TypeLnc $type_lnc): ?float
    {
        return $this->db->repository('lnc.uvue')
            ->createQuery()
            ->and('type_local_non_chauffe', $type_lnc)
            ->getOne()
            ?->floatval('uvue');
    }

    public function b(
        float $uvue,
        EtatIsolation $isolation_aiu,
        EtatIsolation $isolation_aue,
        float $aiu,
        float $aue,
    ): ?float {
        $aiu_aue = $aiu / $aue;
        return $this->db->repository('lnc.b')
            ->createQuery()
            ->and('uvue', $uvue)
            ->and('isolation_aiu', $isolation_aiu)
            ->and('isolation_aue', $isolation_aue)
            ->andCompareTo('aiu_aue', $aiu_aue)
            ->getOne()
            ?->floatval('b');
    }

    public function c1(
        Mois $mois,
        ZoneClimatique $zone_climatique,
        Inclinaison $inclinaison,
        ?Orientation $orientation
    ): ?float {
        $records = $this->db->repository('paroi.c1')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->and('orientation', $orientation)
            ->andCompareTo('inclinaison', $inclinaison->value())
            ->getMany();

        return $records->find(name: 'mois', value: $mois->value)?->floatval('c1');
    }

    public function t(
        TypeBaie $type_baie,
        ?Materiau $materiau,
        ?bool $presence_rupteur_pont_thermique,
        ?TypeVitrage $type_vitrage,
    ): ?float {
        return $this->db->repository('lnc.t')
            ->createQuery()
            ->and('type_baie', $type_baie)
            ->and('materiau', $materiau)
            ->and('presence_rupteur_pont_thermique', $presence_rupteur_pont_thermique)
            ->and('type_vitrage', $type_vitrage)
            ->getOne()
            ?->floatval('t');
    }
}
