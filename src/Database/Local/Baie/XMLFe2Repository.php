<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Data\{Fe2, Fe2Repository};
use App\Domain\Baie\Enum\TypeMasqueLointain;
use App\Domain\Common\Enum\Orientation;

final class XMLFe2Repository implements Fe2Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.fe2';
    }

    public function find_by(
        TypeMasqueLointain $type_masque_lointain,
        Orientation $orientation_baie,
        float $hauteur_masque_alpha,
    ): ?Fe2 {
        $record = $this->createQuery()
            ->and('type_masque_lointain', $type_masque_lointain->id())
            ->and('orientation_baie', $orientation_baie->id())
            ->andCompareTo('hauteur_masque_alpha', $hauteur_masque_alpha)
            ->getOne();

        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Fe2
    {
        return new Fe2(fe2: $record->get('fe2')->floatval());
    }
}
