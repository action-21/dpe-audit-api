<?php

namespace App\Database\Local\Enveloppe;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete};
use App\Domain\Enveloppe\Service\ParoiTableValeurRepository;
use App\Database\Local\{XMLTableDatabase, XMLTableElement};

abstract class XMLParoiTableValeurRepository implements ParoiTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function b(Mitoyennete $mitoyennete): ?float
    {
        return $this->db->repository('paroi.b')
            ->createQuery()
            ->and('mitoyennete', $mitoyennete)
            ->getOne()
            ?->floatval('b');
    }

    public function bver(
        ZoneClimatique $zone_climatique,
        EtatIsolation $isolation_paroi,
        array $orientations_lnc
    ): ?float {
        $records = $this->db->repository('paroi.bver')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('isolation_paroi', $isolation_paroi)
            ->getMany()
            ->filter(fn(XMLTableElement $record) => in_array(
                $record->strval('orientation_lnc'),
                array_column($orientations_lnc, 'value')
            ));

        if (0 === $records->count()) {
            return null;
        }
        return $records->reduce(
            fn(?float $bver, XMLTableElement $record) => $bver + $record->floatval('bver')
        ) / $records->count();
    }
}
