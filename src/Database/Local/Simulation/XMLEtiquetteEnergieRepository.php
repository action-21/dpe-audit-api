<?php

namespace App\Database\Local\Simulation;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Simulation\Data\EtiquetteEnergieRepository;
use App\Domain\Simulation\Enum\Etiquette;

final class XMLEtiquetteEnergieRepository implements EtiquetteEnergieRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'common.etiquette_energie';
    }

    public function find(ZoneClimatique $zone_climatique, int $altitude, float $cep, float $eges): ?Etiquette
    {
        $record = $this->createQuery()
            ->and('zone_climatique', $zone_climatique->id(), true)
            ->andCompareTo('altitude', $altitude)
            ->andCompareTo('cep', $cep)
            ->andCompareTo('eges', $eges)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Etiquette
    {
        return Etiquette::from($record->get('etiquette')->strval());
    }
}
