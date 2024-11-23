<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Data\{Tfonc100, Tfonc100Repository};
use App\Domain\Chauffage\Enum\TemperatureDistribution;

final class XMLTfonc100Repository implements Tfonc100Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.tfonc100';
    }

    public function find_by(
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_emetteur,
    ): ?Tfonc100 {
        $record = $this->createQuery()
            ->and('temperature_distribution', $temperature_distribution->value)
            ->andCompareTo('annee_installation_emetteur', $annee_installation_emetteur)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Tfonc100
    {
        return new Tfonc100(tfonc100: $element->get('tfonc100')->floatval());
    }
}
