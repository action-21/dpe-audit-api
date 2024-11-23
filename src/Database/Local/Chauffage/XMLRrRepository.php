<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Data\{Rr, RrRepository};
use App\Domain\Chauffage\Enum\{LabelGenerateur, TypeEmission, TypeGenerateur};

final class XMLRrRepository implements RrRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.rr';
    }

    public function find_by(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
        ?bool $reseau_collectif,
        ?bool $presence_robinet_thermostatique,
        ?bool $presence_regulation_terminale,
    ): ?Rr {
        $record = $this->createQuery()
            ->and('type_emission', $type_emission->value)
            ->and('type_generateur', $type_generateur->value, true)
            ->and('label_generateur', $label_generateur?->value, true)
            ->and('reseau_collectif', $reseau_collectif, true)
            ->and('presence_robinet_thermostatique', $presence_robinet_thermostatique, true)
            ->and('presence_regulation_terminale', $presence_regulation_terminale, true)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Rr
    {
        return new Rr(rr: $element->get('rr')->floatval(),);
    }
}
