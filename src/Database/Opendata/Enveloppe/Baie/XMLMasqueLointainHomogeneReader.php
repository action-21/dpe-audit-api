<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Common\ValueObject\Orientation;
use App\Domain\Enveloppe\Enum\Baie\TypeMasqueLointain;

/**
 * Données reconstituées pour chaque baie
 */
final class XMLMasqueLointainHomogeneReader extends XMLMasqueLointainReader
{
    public function supports(): bool
    {
        return $this->tv_coef_masque_lointain_homogene_id() !== null;
    }

    public function baie(): XMLBaieReader
    {
        return XMLBaieReader::from($this->xml());
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

    public function orientation(): Orientation
    {
        return $this->baie()->orientation();
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
        return $this->findOne('.//tv_coef_masque_lointain_homogene_id')?->intval();
    }
}
