<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Data\{Omb, OmbRepository};
use App\Domain\Baie\Enum\{SecteurChampsVision, TypeMasqueLointain};
use App\Domain\Common\Enum\Orientation;

final class XMLOmbRepository implements OmbRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.omb';
    }

    public function find_by(
        TypeMasqueLointain $type_masque_lointain,
        Orientation $orientation_baie,
        SecteurChampsVision $secteur,
        float $hauteur_masque_alpha,
    ): ?Omb {
        $record = $this->createQuery()
            ->and('type_masque_lointain', $type_masque_lointain->id())
            ->and('secteur', $secteur->id())
            ->and('orientation_baie', $orientation_baie->id())
            ->andCompareTo('hauteur_masque_alpha', $hauteur_masque_alpha)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Omb
    {
        return new Omb(omb: $record->get('omb')->floatval());
    }
}
