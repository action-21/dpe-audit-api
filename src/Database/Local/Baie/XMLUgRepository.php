<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Data\{Ug, UgRepository, UgCollection};
use App\Domain\Baie\Enum\{NatureGazLame, TypeBaie, TypeSurvitrage, TypeVitrage};

final class XMLUgRepository implements UgRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.ug';
    }

    public function search_by(
        TypeBaie $type_baie,
        ?TypeVitrage $type_vitrage,
        ?TypeSurvitrage $type_survitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?float $inclinaison_vitrage,
    ): UgCollection {
        return new UgCollection(\array_map(
            fn(XMLTableElement $record): Ug => $this->to($record),
            $this->createQuery()
                ->and('type_baie', $type_baie->id(), true)
                ->and('type_vitrage', $type_vitrage?->id(), true)
                ->and('presence_survitrage', $type_survitrage !== null, true)
                ->and('nature_gaz_lame', $nature_gaz_lame?->id(), true)
                ->andCompareTo('inclinaison_vitrage', $inclinaison_vitrage)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): Ug
    {
        return new Ug(
            epaisseur_lame: $record->get('epaisseur_lame')->floatval(),
            ug: $record->get('ug')->floatval(),
        );
    }
}
