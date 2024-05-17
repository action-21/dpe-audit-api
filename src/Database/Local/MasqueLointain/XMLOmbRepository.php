<?php

namespace App\Database\Local\MasqueLointain;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\Orientation;
use App\Domain\MasqueLointain\Enum\SecteurOrientation;
use App\Domain\MasqueLointain\Table\{Omb, OmbCollection, OmbRepository};

final class XMLOmbRepository implements OmbRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'masque_lointain.omb.xml';
    }

    public function find(int $id): ?Omb
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(SecteurOrientation $secteur_orientation, Orientation $orientation, float $hauteur_alpha): ?Omb
    {
        $record = $this->createQuery()
            ->and(\sprintf('secteur_orientation_id = "%s"', $secteur_orientation->id()))
            ->and(\sprintf('orientation = "%s"', $orientation->code()))
            ->andCompareTo('hauteur_alpha', $hauteur_alpha)
            ->getOne();

        return $record ? $this->to($record) : null;
    }

    public function search_by(
        ?SecteurOrientation $secteur_orientation = null,
        ?Orientation $orientation = null,
        ?float $hauteur_alpha = null,
        ?int $tv_coef_masque_lointain_non_homogene_id = null
    ): OmbCollection {
        $this->createQuery();

        if ($secteur_orientation) {
            $this->and(\sprintf('secteur_orientation_id = "%s"', $secteur_orientation->id()));
        }
        if ($orientation) {
            $this->and(\sprintf('orientation = "%s"', $orientation->code()));
        }
        if ($hauteur_alpha) {
            $this->andCompareTo('hauteur_alpha', $hauteur_alpha);
        }
        if ($tv_coef_masque_lointain_non_homogene_id) {
            $this->and(\sprintf('tv_coef_masque_lointain_non_homogene_id = "%s"', $tv_coef_masque_lointain_non_homogene_id));
        }
        return new OmbCollection(\array_map(
            fn (XMLTableElement $record): Omb => $this->to($record),
            $this->getMany(),
        ));
    }

    public function to(XMLTableElement $record): Omb
    {
        return new Omb(
            id: $record->id(),
            tv_coef_masque_lointain_non_homogene_id: (int) $record->tv_coef_masque_lointain_non_homogene_id,
            secteur_orientation: SecteurOrientation::from((int) $record->secteur_orientation),
            orientation: Orientation::from_code((string) $record->orientation),
            hauteur_alpha_defaut: (float) $record->hauteur_alpha_defaut,
            omb: (float) $record->omb,
        );
    }
}
