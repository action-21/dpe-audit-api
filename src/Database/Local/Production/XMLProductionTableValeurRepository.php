<?php

namespace App\Database\Local\Production;

use App\Domain\Common\ValueObject\{Orientation, Inclinaison};
use App\Domain\Production\Service\ProductionTableValeurRepository;
use App\Database\Local\XMLTableDatabase;

final class XMLProductionTableValeurRepository implements ProductionTableValeurRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    public function kpv(Orientation $orientation, Inclinaison $inclinaison): ?float
    {
        return $this->db->repository('production.kpv')
            ->createQuery()
            ->andCompareTo('orientation_pv', $orientation->value())
            ->andCompareTo('inclinaison_pv', $inclinaison->value())
            ->getOne()
            ?->floatval('kpv');
    }
}
