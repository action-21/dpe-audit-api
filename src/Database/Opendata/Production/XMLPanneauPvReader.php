<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\{Id, Inclinaison, Orientation};

final class XMLPanneauPvReader extends XMLReader
{
    public function supports(): bool
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

    public function orientation(): Orientation
    {
        return match ($this->enum_orientation_pv_id()) {
            1 => Orientation::from(90),
            2 => Orientation::from(135),
            3 => Orientation::from(180),
            4 => Orientation::from(225),
            5 => Orientation::from(270),
            default => match ($this->tv_coef_orientation_pv_id()) {
                1, 6, 11, 16 => Orientation::from(90),
                2, 7, 12, 17 => Orientation::from(135),
                3, 8, 13, 18 => Orientation::from(180),
                4, 9, 14, 19 => Orientation::from(225),
                5, 10, 15, 20 => Orientation::from(270),
            }
        };
    }

    public function inclinaison(): Inclinaison
    {
        return match ($this->enum_inclinaison_pv_id()) {
            1 => Inclinaison::from(10),
            2 => Inclinaison::from(30),
            3 => Inclinaison::from(60),
            4 => Inclinaison::from(80),
            default => match ($this->tv_coef_orientation_pv_id()) {
                1, 2, 3, 4, 5 => Inclinaison::from(10),
                6, 7, 8, 9, 10 => Inclinaison::from(30),
                11, 12, 13, 14, 15 => Inclinaison::from(60),
                16, 17, 18, 19, 20 => Inclinaison::from(80),
            }
        };
    }

    public function surface_capteurs(): ?float
    {
        return $this->surface_totale_capteurs() ? $this->surface_totale_capteurs() / $this->modules() : null;
    }

    public function surface_totale_capteurs(): ?float
    {
        return $this->findOne('.//surface_totale_capteurs')?->floatval();
    }

    public function modules(): int
    {
        return $this->findOne('.//nombre_module')?->intval() ?? 1;
    }

    public function enum_orientation_pv_id(): ?int
    {
        return $this->findOne('.//enum_orientation_pv_id')?->intval();
    }

    public function enum_inclinaison_pv_id(): ?int
    {
        return $this->findOne('.//enum_inclinaison_pv_id')?->intval();
    }

    public function tv_coef_orientation_pv_id(): ?int
    {
        return $this->findOne('.//tv_coef_orientation_pv_id')?->intval();
    }
}
