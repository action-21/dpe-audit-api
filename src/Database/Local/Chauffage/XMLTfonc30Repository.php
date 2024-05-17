<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeGenerateur};
use App\Domain\Chauffage\Table\{Tfonc30, Tfonc30Repository};

final class XMLTfonc30Repository implements Tfonc30Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.emission.tfonc30.xml';
    }

    public function find(int $id): ?Tfonc30
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(
        TypeGenerateur $type_generateur,
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_emetteur
    ): ?Tfonc30 {
        $record = $this->createQuery()
            ->and(\sprintf('type_generateur_id = "%s"', $type_generateur->id()))
            ->and(\sprintf('temperature_distribution_id = "%s"', $temperature_distribution->id()))
            ->andCompareTo('annee_installation_emetteur', $annee_installation_emetteur)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Tfonc30
    {
        return new Tfonc30(id: $record->id(), tfonc30: (float) $record->tfonc30);
    }
}
