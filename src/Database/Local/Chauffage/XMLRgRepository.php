<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Data\{Rg, RgRepository};
use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};

final class XMLRgRepository implements RgRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.rg';
    }

    public function find_by(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ?LabelGenerateur $label_generateur,
        int $annee_installation_generateur,
    ): ?Rg {
        $record = $this->createQuery()
            ->and('type_generateur', $type_generateur->value)
            ->and('energie_generateur', $energie_generateur->value, true)
            ->and('label_generateur', $label_generateur?->value, true)
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Rg
    {
        return new Rg(rg: $element->get('rg')->floatval(),);
    }
}
