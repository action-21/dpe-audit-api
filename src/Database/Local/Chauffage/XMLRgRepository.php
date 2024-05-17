<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Chauffage\Table\{Rg, RgRepository};

final class XMLRgRepository implements RgRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.generateur.rg.xml';
    }

    public function find(int $id): ?Rg
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeGenerateur $type_generateur, ?int $annee_installation_generateur): ?Rg
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_generateur_id = "%s"', $type_generateur->id()))
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Rg
    {
        return new Rg(
            id: $record->id(),
            rg: (float) $record->rg,
        );
    }
}
