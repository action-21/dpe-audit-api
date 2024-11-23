<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Data\{Scop, ScopRepository};
use App\Domain\Chauffage\Enum\{TypeEmission, TypeGenerateur};
use App\Domain\Common\Enum\ZoneClimatique;

final class XMLScopRepository implements ScopRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.scop';
    }

    public function find_by(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        TypeEmission $type_emission,
        int $annee_installation_generateur,
    ): ?Scop {
        $record = $this->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('type_generateur', $type_generateur->value)
            ->and('type_emission', $type_emission->value)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Scop
    {
        return new Scop(
            scop: $element->get('scop')->floatval(),
            cop: $element->get('cop')->floatval(),
        );
    }
}
