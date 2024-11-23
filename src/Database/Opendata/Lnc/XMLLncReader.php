<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\{XMLElement, XMLReader};
use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Enum\{EtatIsolation, Mitoyennete, NatureMenuiserie, TypeBaie, TypeLnc, TypeVitrage};

/**
 * Les locaux non chauffés sont reconstruits depuis chaque paroi
 */
final class XMLLncReader extends XMLReader
{
    public function __construct(private XMLBaieReader $baie_reader) {}

    public function apply(): bool
    {
        return TypeLnc::from_type_adjacence_id($this->enum_type_adjacence_id()) !== null;
    }

    public function read_baies(): XMLBaieReader
    {
        return $this->baie_reader->read($this->ets()->findMany('.//baie_ets_collection/baie_ets'));
    }

    public function ets(): XMLElement
    {
        return $this->xml()->findOneOrError('//ets/donnee_entree/reference/' . $this->reference_lnc());
    }

    public function id(): Id
    {
        return Id::create();
    }

    public function description(): string
    {
        return 'Local non chauffé non décrit';
    }

    public function type_lnc(): TypeLnc
    {
        return TypeLnc::from_type_adjacence_id($this->enum_type_adjacence_id());
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

    public function isolation_paroi_aue(): EtatIsolation
    {
        return match ($this->enum_cfg_isolation_lnc_id()) {
            2, 4 => EtatIsolation::NON_ISOLE,
            3, 5 => EtatIsolation::ISOLE,
        };
    }

    public function isolation_paroi_aiu(): EtatIsolation
    {
        return match ($this->enum_cfg_isolation_lnc_id()) {
            2, 3 => EtatIsolation::NON_ISOLE,
            4, 5 => EtatIsolation::ISOLE,
        };
    }

    public function reference_lnc(): string
    {
        return $this->xml()->findOneOrError('.//reference_lnc')->strval();
    }

    public function enum_type_adjacence_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_adjacence_id')->intval();
    }

    public function enum_cfg_isolation_lnc_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_cfg_isolation_lnc_id')->intval();
    }

    public function tv_coef_transparence_ets_id(): int
    {
        return $this->ets()->findOneOrError('.//tv_coef_transparence_ets_id')->intval();
    }

    public function surface_aue(): float
    {
        return $this->xml()->findOneOrError('.//surface_aue')->floatval();
    }

    /**
     * TODO: Vérifier la surface unitaire des portes / baies
     */
    public function surface_paroi(): float
    {
        return $this->xml()->findOneOfOrError([
            './/surface_paroi_opaque',
            './/surface_totale_baie',
            './/surface_porte',
        ])->floatval();
    }

    public function surface_paroi_totale(): float
    {
        if ($value = $this->xml()->findOne('.//surface_paroi_opaque')?->floatval()) {
            $reference = $this->xml()->findOneOrError('.//reference')->strval();

            foreach ($this->xml()->baie_collection() as $item) {
                if ($item->findOne('.//reference_paroi')?->strval() === $reference)
                    $value += $item->findOneOrError('.//surface_totale_baie')->floatval();
            }
            foreach ($this->xml()->audit()->porte_collection() as $item) {
                if ($item->findOne('.//reference_paroi')?->strval() === $reference)
                    $value += $item->findOneOrError('.//surface_porte')->floatval();
            }
            return $value;
        }
        return $this->xml()->findOneOfOrError([
            './/surface_paroi_opaque',
            './/surface_totale_baie',
            './/surface_porte',
        ])->floatval();
    }

    public function surface_aiu(): float
    {
        return $this->xml()->findOneOrError('.//surface_aiu')->floatval();
    }
}
