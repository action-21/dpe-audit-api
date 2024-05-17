<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Chauffage\Enum\{TypeEmission, TypeGenerateur};
use App\Domain\Chauffage\Table\{Scop, ScopRepository};

final class XMLScopRepository implements ScopRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.generateur.scop.xml';
    }

    public function find(int $id): ?Scop
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        ?TypeEmission $type_emission,
        ?int $anne_installation_generateur,
    ): ?Scop {
        $record = $this->createQuery()
            ->and(\sprintf('zone_climatique = "%s"', $zone_climatique->lib()))
            ->and(\sprintf('type_generateur_id = "%s"', $type_generateur->id()))
            ->and(\sprintf('plancher_chauffant = "%s" or plancher_chauffant = ""', (int) ($type_emission === TypeEmission::PLANCHER_CHAUFFANT)))
            ->and(\sprintf('plafond_chauffant = "%s" or plafond_chauffant = ""', (int) ($type_emission === TypeEmission::PLAFOND_CHAUFFANT)))
            ->andCompareTo('annee_installation_generateur', $anne_installation_generateur)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Scop
    {
        return new Scop(
            id: $record->id(),
            cop: ($value = $record->cop) ? (float) $value : null,
            scop: ($value = $record->scop) ? (float) $value : null,
        );
    }
}
