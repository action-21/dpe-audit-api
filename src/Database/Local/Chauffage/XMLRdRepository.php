<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeDistribution, TypeInstallation};
use App\Domain\Chauffage\Table\{Rd, RdRepository};

final class XMLRdRepository implements RdRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.emission.rd.xml';
    }

    public function find(int $id): ?Rd
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(
        TypeInstallation $type_installation,
        TypeDistribution $type_distribution,
        ?TemperatureDistribution $temperature_distribution,
        ?bool $reseau_distribution_isole
    ): ?Rd {
        $record = $this->createQuery()
            ->and(\sprintf('type_distribution_id = "%s"', $type_distribution->id()))
            ->and(\sprintf('installation_collective = "%s" or installation_collective = ""', (int) $type_installation->installation_collective()))
            ->and(\sprintf('distribution_haute_temperature = "%s" or distribution_haute_temperature = ""', $temperature_distribution ? (int) $temperature_distribution->haute_temperature() : null))
            ->and(\sprintf('reseau_distribution_isole = "%s" or reseau_distribution_isole = ""', null !== $reseau_distribution_isole ? (int) $reseau_distribution_isole : null))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Rd
    {
        return new Rd(
            id: $record->id(),
            rd: (float) $record->rd,
        );
    }
}
