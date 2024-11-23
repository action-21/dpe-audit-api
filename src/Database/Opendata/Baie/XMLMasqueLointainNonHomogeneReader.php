<?php

namespace App\Database\Opendata\Baie;

use App\Database\Opendata\XMLReaderIterator;
use App\Domain\Baie\Enum\TypeMasqueLointain;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Type\Id;

final class XMLMasqueLointainNonHomogeneReader extends XMLReaderIterator
{
    public function id(): Id
    {
        return Id::create();
    }

    public function description(): string
    {
        return 'Masque lointain non homogène';
    }

    public function type_masque(): TypeMasqueLointain
    {
        return TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE;
    }

    public function orientation(float $orientation_baie): float
    {
        $orientation_baie_enum = Orientation::from_azimut($orientation_baie);
        return match ($this->tv_coef_masque_lointain_non_homogene_id()) {
            1, 2, 3, 4 => $orientation_baie + 77.5,
            5, 6, 7, 8 => $orientation_baie + 77.5,
            9, 10, 11, 12 => $orientation_baie_enum === Orientation::EST ? $orientation_baie + 77.5 : $orientation_baie - 77.5,
            13, 14, 15, 16 => $orientation_baie_enum === Orientation::EST ? $orientation_baie + 22.5 : $orientation_baie - 22.5,
            17, 18, 19, 20 => $orientation_baie_enum === Orientation::EST ? $orientation_baie - 22.5 : $orientation_baie + 22.5,
        };
    }

    public function hauteur(): float
    {
        return match ($this->tv_coef_masque_lointain_non_homogene_id()) {
            1, 5, 9, 13, 17 => 7.5,
            2, 6, 10, 14, 18 => 22.5,
            3, 7, 11, 15, 19 => 45,
            4, 8, 12, 16, 20 => 60,
        };
    }

    public function tv_coef_masque_lointain_non_homogene_id(): int
    {
        return $this->xml()->findOneOrError('.//tv_coef_masque_lointain_non_homogene_id')->intval();
    }
}
