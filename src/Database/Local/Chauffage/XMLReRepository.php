<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Data\{Re, ReRepository};
use App\Domain\Chauffage\Enum\{LabelGenerateur, TypeEmission, TypeGenerateur};

final class XMLReRepository implements ReRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.re';
    }

    public function find_by(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
    ): ?Re {
        $record = $this->createQuery()
            ->and('type_emission', $type_emission->value)
            ->and('type_generateur', $type_generateur->value, true)
            ->and('label_generateur', $label_generateur?->value, true)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Re
    {
        return new Re(re: $element->get('re')->floatval(),);
    }
}
