<?php

namespace App\Database\Local\Eclairage;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Eclairage\Service\EclairageTableValeurRepository;
use App\Database\Local\XMLTableDatabase;

final class XMLEclairageTableValeurRepository implements EclairageTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function nhecl(ZoneClimatique $zone_climatique): ?float
    {
        return $this->db->repository('eclairage.nhecl')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->getOne()
            ?->floatval('nhecl');
    }
}
