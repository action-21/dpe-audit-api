<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Domain\Common\Enum\Orientation as OrientationEnum;
use App\Domain\Common\ValueObject\{Id, Orientation};
use App\Domain\Enveloppe\Enum\Baie\TypeMasqueLointain;

final class XMLMasqueLointainNonHomogeneReader extends XMLMasqueLointainReader
{
    public function baie(): XMLBaieReader
    {
        return XMLBaieReader::from($this->findOneOrError('//ancestor::baie_vitree'));
    }

    public function supports(): bool
    {
        return $this->baie()->orientation() !== null;
    }

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

    public function orientation(): Orientation
    {
        $orientation_baie = $this->baie()->orientation();
        $orientation_baie_enum = $orientation_baie->enum();

        $orientation = match ($this->tv_coef_masque_lointain_non_homogene_id()) {
            1, 2, 3, 4 => $orientation_baie + 77.5,
            5, 6, 7, 8 => $orientation_baie + 77.5,
            9, 10, 11, 12 => $orientation_baie_enum === OrientationEnum::EST ? $orientation_baie + 77.5 : $orientation_baie - 77.5,
            13, 14, 15, 16 => $orientation_baie_enum === OrientationEnum::EST ? $orientation_baie + 22.5 : $orientation_baie - 22.5,
            17, 18, 19, 20 => $orientation_baie_enum === OrientationEnum::EST ? $orientation_baie - 22.5 : $orientation_baie + 22.5,
        };
        return Orientation::from($orientation);
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
        return $this->findOneOrError('.//tv_coef_masque_lointain_non_homogene_id')->intval();
    }
}
