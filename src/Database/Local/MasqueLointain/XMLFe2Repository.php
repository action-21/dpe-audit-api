<?php

namespace App\Database\Local\MasqueLointain;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\Orientation;
use App\Domain\MasqueLointain\Table\{Fe2, Fe2Collection, Fe2Repository};

final class XMLFe2Repository implements Fe2Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'masque_lointain.fe2.xml';
    }

    public function find(int $id): ?Fe2
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(Orientation $orientation, float $hauteur_alpha): ?Fe2
    {
        $record = $this->createQuery()
            ->and(\sprintf('orientation = "%s"', $orientation->code()))
            ->andCompareTo('hauteur_alpha', $hauteur_alpha)
            ->getOne();

        return $record ? $this->to($record) : null;
    }

    public function search_by(?Orientation $orientation = null, ?float $hauteur_alpha = null, ?int $opendata_id = null): Fe2Collection
    {
        $this->createQuery();

        if ($orientation) {
            $this->and(\sprintf('orientation = "%s"', $orientation->code()));
        }
        if ($hauteur_alpha) {
            $this->andCompareTo('hauteur_alpha', $hauteur_alpha);
        }
        if ($opendata_id) {
            $this->and(\sprintf('opendata_id = "%s"', $opendata_id));
        }
        return new Fe2Collection(\array_map(
            fn (XMLTableElement $record): Fe2 => $this->to($record),
            $this->getMany(),
        ));
    }

    public function to(XMLTableElement $record): Fe2
    {
        return new Fe2(
            id: $record->id(),
            orientation: Orientation::from_code((string) $record->orientation),
            hauteur_alpha_defaut: (float) $record->hauteur_alpha_defaut,
            fe2: (float) $record->fe2,
            tv_coef_masque_lointain_homogene_id: (int) $record->opendata_id,
        );
    }
}
