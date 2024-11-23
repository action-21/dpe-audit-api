<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Data\{I0, I0Repository};
use App\Domain\Chauffage\Enum\{TypeEmission, TypeIntermittence};
use App\Domain\Common\Enum\Enum;

final class XMLI0Repository implements I0Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.i0';
    }

    public function find_by(
        Enum $type_batiment,
        TypeEmission $type_emission,
        TypeIntermittence $type_intermittence,
        bool $chauffage_central,
        bool $regulation_terminale,
        bool $chauffage_collectif,
        bool $inertie_lourde,
        ?bool $comptage_individuel,
    ): ?I0 {
        $record = $this->createQuery()
            ->and('type_batiment', $type_batiment->id())
            ->and('type_emission', $type_emission->id())
            ->and('type_intermittence', $type_intermittence->id())
            ->and('chauffage_central', $chauffage_central)
            ->and('regulation_terminale', $regulation_terminale, true)
            ->and('chauffage_collectif', $chauffage_collectif, true)
            ->and('inertie_lourde', $inertie_lourde, true)
            ->and('comptage_individuel', $comptage_individuel, true)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): I0
    {
        return new I0(i0: $element->get('i0')->floatval(),);
    }
}
