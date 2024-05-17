<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Enum\TemperatureDistribution;
use App\Domain\Chauffage\Table\{Tfonc100, Tfonc100Repository};

final class XMLTfonc100Repository implements Tfonc100Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.emission.tfonc100.xml';
    }

    public function find(int $id): ?Tfonc100
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TemperatureDistribution $temperature_distribution, int $annee_installation_emetteur): ?Tfonc100
    {
        $record = $this->createQuery()
            ->and(\sprintf('temperature_distribution_id = "%s"', $temperature_distribution->id()))
            ->andCompareTo('annee_installation_emetteur', $annee_installation_emetteur,)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Tfonc100
    {
        return new Tfonc100(id: $record->id(), tfonc100: (float) $record->tfonc100);
    }
}
