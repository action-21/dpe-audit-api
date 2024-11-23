<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Baie\Data\{Fe1, Fe1Repository};
use App\Domain\Baie\Enum\TypeMasqueProche;

final class XMLFe1Repository implements Fe1Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'masque_proche.fe1';
    }

    public function find_by(
        TypeMasqueProche $type_masque_proche,
        float $avancee_masque,
        ?Orientation $orientation_baie,
    ): ?Fe1 {
        $record = $this->createQuery()
            ->and('type_masque_proche', $type_masque_proche->id())
            ->and('orientation_baie', $orientation_baie?->id())
            ->andCompareTo('avancee_masque', $avancee_masque)
            ->getOne();

        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Fe1
    {
        return new Fe1(fe1: $record->get('fe1')->floatval(),);
    }
}
