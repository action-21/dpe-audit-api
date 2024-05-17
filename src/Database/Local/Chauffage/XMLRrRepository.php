<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Enum\{TypeDistribution, TypeEmission, TypeGenerateur, TypeInstallation};
use App\Domain\Chauffage\Table\{Rr, RrRepository};

final class XMLRrRepository implements RrRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.emission.rr.xml';
    }

    public function find(int $id): ?Rr
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(
        TypeInstallation $type_installation,
        TypeEmission $type_emission,
        TypeDistribution $type_distribution,
        TypeGenerateur $type_generateur,
    ): ?Rr
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_emission_id = "%s" or type_emission_id=""', $type_emission->id()))
            ->and(\sprintf('type_distribution_id = "%s" or type_distribution_id = ""', $type_distribution->id()))
            ->and(\sprintf('type_generateur_id = "%s" or type_generateur_id = ""', $type_generateur->id()))
            ->and(\sprintf('installation_collective = "%s" or installation_collective = ""', (int) $type_installation->installation_collective()))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Rr
    {
        return new Rr(
            id: $record->id(),
            rr: (float) $record->rr,
        );
    }
}
