<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Enum\{NatureMenuiserie, TypeBaie, TypeVitrage};

final class XMLEtsReader extends XMLReader
{
    public function read_baies(): XMLBaieReader
    {
        return XMLBaieReader::from($this->xml()->findManyOrError('.//baie_ets_collection/baie_ets'));
    }

    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Espace tampon solarisé non décrit';
    }

    public function type_baie(): TypeBaie
    {
        return TypeBaie::from_tv_coef_transparence_ets_id($this->tv_coef_transparence_ets_id());
    }

    public function nature_menuiserie(): ?NatureMenuiserie
    {
        return NatureMenuiserie::from_tv_coef_transparence_ets_id($this->tv_coef_transparence_ets_id());
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

    public function tv_coef_transparence_ets_id(): int
    {
        return $this->xml()->findOneOrError('.//tv_coef_transparence_ets_id')->intval();
    }

    public function tv_coef_reduction_deperdition_id(): int
    {
        return $this->xml()->findOneOrError('.//tv_coef_reduction_deperdition_id')->intval();
    }

    public function enum_cfg_isolation_lnc_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_cfg_isolation_lnc_id')->intval();
    }
}
