<?php

namespace App\Database\Local\Chauffage;

use App\Domain\Chauffage\Data\{Paux, PauxRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};

final class XMLPauxRepository implements PauxRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.paux';
    }

    public function find_by(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur, ?bool $presence_ventouse): ?Paux
    {
        $record = $this->createQuery()
            ->and('type_generateur', $type_generateur->value)
            ->and('energie_generateur', $energie_generateur->value, true)
            ->and('presence_ventouse', $presence_ventouse, true)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Paux
    {
        return new Paux(
            g: $record->get('G')->floatval(),
            h: $record->get('H')->floatval(),
            pn_max: $record->get('pn_max')->floatval(),
        );
    }
}
