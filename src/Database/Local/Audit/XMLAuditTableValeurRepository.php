<?php

namespace App\Database\Local\Audit;

use App\Domain\Audit\Service\AuditTableValeurRepository;
use App\Domain\Audit\ValueObject\{SollicitationExterieure, SollicitationsExterieures};
use App\Domain\Common\Enum\{ZoneClimatique, Mois};
use App\Database\Local\{XMLTableDatabase, XMLTableElement};
use App\Domain\Audit\Enum\Etiquette;

final class XMLAuditTableValeurRepository implements AuditTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function sollicitations_exterieures(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        bool $parois_anciennes_lourdes
    ): ?SollicitationsExterieures {
        $records = $this->db->repository('audit.ext')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->and('parois_anciennes_lourdes', $parois_anciennes_lourdes)
            ->andCompareTo('altitude', $altitude)
            ->getMany()
            ->map(fn(XMLTableElement $item) => SollicitationExterieure::create(
                mois: Mois::from($item->strval('mois')),
                epv: $item->floatval('epv'),
                e: $item->floatval('e'),
                efr26: $item->floatval('efr26'),
                efr28: $item->floatval('efr28'),
                nref19: $item->floatval('nref19'),
                nref21: $item->floatval('nref21'),
                nref26: $item->floatval('nref26'),
                nref28: $item->floatval('nref28'),
                dh14: $item->floatval('dh14'),
                dh19: $item->floatval('dh19'),
                dh21: $item->floatval('dh21'),
                dh26: $item->floatval('dh26'),
                dh28: $item->floatval('dh28'),
                tefs: $item->floatval('tefs'),
                text: $item->floatval('text'),
                textmoy_clim26: $item->floatval('textmoy_clim26'),
                textmoy_clim28: $item->floatval('textmoy_clim28'),
            ));

        return SollicitationsExterieures::create(...$records->values());
    }

    public function tbase(ZoneClimatique $zone_climatique, int|float $altitude): ?float
    {
        return $this->db->repository('audit.tbase')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->andCompareTo('altitude', $altitude)
            ->getOne()
            ?->floatval('tbase');
    }

    public function etiquette_energie(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        float $cep,
        float $eges,
    ): ?Etiquette {
        $value = $this->db->repository('audit.etiquette_energie')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->andCompareTo('altitude', $altitude)
            ->andCompareTo('cep', $cep)
            ->andCompareTo('eges', $eges)
            ->getOne()
            ?->strval('etiquette');

        return $value ? Etiquette::from($value) : null;
    }

    public function etiquette_climat(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        float $eges,
    ): ?Etiquette {
        $value = $this->db->repository('audit.etiquette_climat')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique)
            ->andCompareTo('altitude', $altitude)
            ->andCompareTo('eges', $eges)
            ->getOne()
            ?->strval('etiquette');

        return $value ? Etiquette::from($value) : null;
    }
}
