<?php

namespace App\Database\Local\Simulation;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Simulation\Data\EtiquetteClimatRepository;
use App\Domain\Simulation\Enum\Etiquette;

final class XMLEtiquetteClimatRepository implements EtiquetteClimatRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'common.etiquette_climat';
    }

    public function find(float $eges): ?Etiquette
    {
        $record = $this->createQuery()->andCompareTo('eges', $eges)->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Etiquette
    {
        return Etiquette::from($record->get('etiquette')->strval());
    }
}
