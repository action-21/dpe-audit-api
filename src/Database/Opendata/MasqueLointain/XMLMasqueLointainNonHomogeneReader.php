<?php

namespace App\Database\Opendata\MasqueLointain;

use App\Database\Opendata\{XMLReaderIterator};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Identifier\Uuid;
use App\Domain\MasqueLointain\Enum\{SecteurOrientation, TypeMasqueLointain};
use App\Domain\MasqueLointain\Table\{Omb, OmbRepository};
use App\Domain\MasqueLointain\ValueObject\{HauteurAlpha, OrientationMasque};

final class XMLMasqueLointainNonHomogeneReader extends XMLReaderIterator
{
    public function __construct(private OmbRepository $table_omb_repository)
    {
    }

    public function type_masque_lointain(): TypeMasqueLointain
    {
        return TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE;
    }

    public function tv_coef_masque_lointain_non_homogene_id(): int
    {
        return (int) $this->get()->findOneOrError('.//tv_coef_masque_lointain_non_homogene_id')->getValue();
    }

    public function table_omb(): Omb
    {
        $table = $this->table_omb_repository
            ->search_by(tv_coef_masque_lointain_non_homogene_id: $this->tv_coef_masque_lointain_non_homogene_id())
            ->first();

        if (null === $table) {
            throw new \RuntimeException('Secteur orientation not found');
        }
        return $table;
    }

    // Données déduites

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function description(): string
    {
        return 'Masque lointain non homogène non décrit';
    }

    public function secteur_orientation(): SecteurOrientation
    {
        return $this->table_omb()->secteur_orientation;
    }

    public function enum_orientation(): Orientation
    {
        return $this->table_omb()->orientation;
    }

    public function orientation(): OrientationMasque
    {
        return OrientationMasque::from($this->enum_orientation()->to_azimut());
    }

    public function hauteur_alpha(): HauteurAlpha
    {
        return HauteurAlpha::from($this->table_omb()->hauteur_alpha_defaut);
    }

    public function read(XMLMasqueLointainReader $reader): self
    {
        $this->array = $reader->get()->findMany('.//masque_lointain_non_homogene');
        return $this;
    }
}
