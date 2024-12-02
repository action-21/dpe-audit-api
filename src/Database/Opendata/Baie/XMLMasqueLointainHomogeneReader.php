<?php

namespace App\Database\Opendata\Baie;

use App\Database\Opendata\XMLReader;
use App\Domain\Baie\Enum\TypeMasqueLointain;
use App\Domain\Common\Type\Id;

final class XMLMasqueLointainHomogeneReader extends XMLReader
{
    public function apply(): bool
    {
        return $this->tv_coef_masque_lointain_homogene_id() !== null;
    }

    public function id(): Id
    {
        return Id::create();
    }

    public function description(): string
    {
        return 'Masque lointain homogène';
    }

    public function type_masque(): TypeMasqueLointain
    {
        return TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE;
    }

    public function hauteur(): float
    {
        return match ($this->tv_coef_masque_lointain_homogene_id()) {
            1, 5, 9 => 7.5,
            2, 6, 10 => 22.5,
            3, 7, 11 => 45,
            4, 8, 12 => 60,
        };
    }

    public function tv_coef_masque_lointain_homogene_id(): ?int
    {
        return $this->xml()->findOne('.//tv_coef_masque_lointain_homogene_id')?->intval();
    }
}
