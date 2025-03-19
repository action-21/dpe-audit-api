<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;

final class XMLPanneauPvReader extends XMLReader
{
    public function apply(): bool
    {
        return ($this->enum_orientation_pv_id() && $this->enum_inclinaison_pv_id()) || $this->tv_coef_orientation_pv_id();
    }

    public function id(): Id
    {
        return Id::create();
    }

    public function description(): string
    {
        return 'Panneau photovoltaïque non décrit';
    }

    public function orientation(): float
    {
        return match ($this->enum_orientation_pv_id()) {
            1 => 90,
            2 => 135,
            3 => 180,
            4 => 225,
            5 => 270,
            default => match ($this->tv_coef_orientation_pv_id()) {
                1, 6, 11, 16 => 90,
                2, 7, 12, 17 => 135,
                3, 8, 13, 18 => 180,
                4, 9, 14, 19 => 225,
                5, 10, 15, 20 => 270,
            }
        };
    }

    public function inclinaison(): float
    {
        return match ($this->enum_inclinaison_pv_id()) {
            1 => 10,
            2 => 30,
            3 => 60,
            4 => 80,
            default => match ($this->tv_coef_orientation_pv_id()) {
                1, 2, 3, 4, 5 => 10,
                6, 7, 8, 9, 10 => 30,
                11, 12, 13, 14, 15 => 60,
                16, 17, 18, 19, 20 => 80,
            }
        };
    }

    public function surface_capteurs(): ?float
    {
        return $this->surface_totale_capteurs() ? $this->surface_totale_capteurs() / $this->modules() : null;
    }

    public function surface_totale_capteurs(): ?float
    {
        return $this->xml()->findOne('.//surface_totale_capteurs')?->floatval();
    }

    public function modules(): int
    {
        return $this->xml()->findOne('.//nombre_module')?->intval() ?? 1;
    }

    public function enum_orientation_pv_id(): ?int
    {
        return $this->xml()->findOne('.//enum_orientation_pv_id')?->intval();
    }

    public function enum_inclinaison_pv_id(): ?int
    {
        return $this->xml()->findOne('.//enum_inclinaison_pv_id')?->intval();
    }

    public function tv_coef_orientation_pv_id(): ?int
    {
        return $this->xml()->findOne('.//tv_coef_orientation_pv_id')?->intval();
    }
}
