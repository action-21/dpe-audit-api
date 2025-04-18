<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\{Id, Inclinaison, Orientation};
use App\Domain\Enveloppe\Enum\Lnc\Materiau;
use App\Domain\Enveloppe\Enum\Lnc\TypeBaie;
use App\Domain\Enveloppe\Enum\Lnc\TypeVitrage;
use App\Domain\Enveloppe\Enum\Mitoyennete;

final class XMLEtsBaieReader extends XMLReader implements XMLLncBaieReader
{
    public function id(): Id
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function paroi_id(): ?Id
    {
        return null;
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? "Baie non décrite";
    }

    public function type(): TypeBaie
    {
        return TypeBaie::from_tv_coef_transparence_ets_id($this->tv_coef_transparence_ets_id());
    }

    public function materiau(): ?Materiau
    {
        return Materiau::from_tv_coef_transparence_ets_id($this->tv_coef_transparence_ets_id());
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return TypeVitrage::from_tv_coef_transparence_ets_id($this->tv_coef_transparence_ets_id());
    }

    public function presence_rupteur_pont_thermique(): ?bool
    {
        return match ($this->tv_coef_transparence_ets_id()) {
            12, 13, 14, 15, 16 => true,
            17, 18, 19, 20, 21 => false,
            default => null,
        };
    }

    public function orientation(): ?Orientation
    {
        return Orientation::from_enum_orientation_id(
            $this->findOneOrError('.//enum_orientation_id')->intval()
        );
    }

    public function inclinaison(): Inclinaison
    {
        return Inclinaison::from_enum_inclinaison_vitrage_id(
            $this->findOneOrError('.//enum_inclinaison_vitrage_id')->intval()
        );
    }

    public function surface(): float
    {
        return $this->surface_totale() / $this->nb_baie();
    }

    public function mitoyennete(): Mitoyennete
    {
        return Mitoyennete::EXTERIEUR;
    }

    public function enum_inclinaison_vitrage_id(): int
    {
        return $this->findOneOrError('.//enum_inclinaison_vitrage_id')->intval();
    }

    public function surface_totale(): float
    {
        return $this->findOneOrError('.//surface_totale_baie')->floatval();
    }

    public function nb_baie(): float
    {
        return $this->findOneOrError('.//nb_baie')->intval();
    }

    public function tv_coef_transparence_ets_id(): int
    {
        return $this->findOneOrError('.//ancestor::ets//tv_coef_transparence_ets_id')->intval();
    }

    public function tv_coef_reduction_deperdition_id(): int
    {
        return $this->findOneOrError('.//ancestor::ets//tv_coef_reduction_deperdition_id')->intval();
    }

    public function enum_cfg_isolation_lnc_id(): int
    {
        return $this->findOneOrError('.//ancestor::ets//enum_cfg_isolation_lnc_id')->intval();
    }
}
