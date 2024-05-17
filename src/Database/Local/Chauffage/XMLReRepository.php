<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Enum\{TypeEmission, TypeGenerateur};
use App\Domain\Chauffage\Table\{Re, ReRepository};

final class XMLReRepository implements ReRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.emission.re.xml';
    }

    public function find(int $id): ?Re
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeEmission $type_emission, TypeGenerateur $type_generateur): ?Re
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_emission_id = "%s"', $type_emission->id()))
            ->and(\sprintf('type_generateur_id = "%s" or type_generateur_id = ""', $type_generateur->id()))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Re
    {
        return new Re(
            id: $record->id(),
            re: (float) $record->re,
        );
    }
}
