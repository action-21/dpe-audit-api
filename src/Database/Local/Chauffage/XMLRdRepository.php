<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Data\{Rd, RdRepository};
use App\Domain\Chauffage\Enum\{IsolationReseau, TemperatureDistribution, TypeDistribution};

final class XMLRdRepository implements RdRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.rd';
    }

    public function find_by(
        TypeDistribution $type_distribution,
        ?TemperatureDistribution $temperature_distribution,
        ?IsolationReseau $isolation_reseau,
        ?bool $reseau_collectif,
    ): ?Rd {
        $record = $this->createQuery()
            ->and('type_distribution', $type_distribution->value)
            ->and('temperature_distribution', $temperature_distribution?->value, true)
            ->and('isolation_reseau', $isolation_reseau?->value, true)
            ->and('reseau_collectif', $reseau_collectif, true)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Rd
    {
        return new Rd(rd: $element->get('rd')->floatval(),);
    }
}
