<?php

namespace App\Database\Opendata\Enveloppe\Niveau;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Inertie;

final class XMLNiveauReader extends XMLReader
{
    public function id(): Id
    {
        return Id::create();
    }

    public function surface(): float
    {
        return $this->audit()->surface_habitable_batiment();
    }

    public function inertie_paroi_verticale(): Inertie
    {
        if ($this->enveloppe()->inertie() === Inertie::TRES_LOURDE) {
            return Inertie::TRES_LOURDE;
        }
        return $this->enveloppe()->inertie_paroi_verticale_lourd() ? Inertie::LOURDE : Inertie::LEGERE;
    }

    public function inertie_plancher_haut(): Inertie
    {
        if ($this->enveloppe()->inertie() === Inertie::TRES_LOURDE) {
            return Inertie::TRES_LOURDE;
        }
        return $this->enveloppe()->inertie_plancher_haut_lourd() ? Inertie::LOURDE : Inertie::LEGERE;
    }

    public function inertie_plancher_bas(): Inertie
    {
        if ($this->enveloppe()->inertie() === Inertie::TRES_LOURDE) {
            return Inertie::TRES_LOURDE;
        }
        return $this->enveloppe()->inertie_plancher_bas_lourd() ? Inertie::LOURDE : Inertie::LEGERE;
    }
}
