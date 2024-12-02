<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Enum\{EtatIsolation, TypeLnc};

/**
 * Les locaux non chauffés sont reconstruits depuis chaque paroi
 */
final class XMLLncReader extends XMLReader
{
    private ?XMLEtsReader $ets_reader = null;

    public function apply(): bool
    {
        return TypeLnc::from_type_adjacence_id($this->enum_type_adjacence_id()) !== null;
    }

    public function read_ets(): ?XMLEtsReader
    {
        if (null === $this->ets_reader) {
            $id = $this->xml()->findOneOfOrError(['.//reference_lnc', './/reference'])->id();
            foreach ($this->xml()->etat_initial()->findManyOrError('.//ets_collection//ets') as $item) {
                if ($item->findOneOrError('./donnee_entree/reference')->id()->compare($id)) {
                    $this->ets_reader = XMLEtsReader::from($item);
                    break;
                }
            }
        }
        return $this->ets_reader;
    }

    public function id(): Id
    {
        return $this->xml()->findOne('.//reference_lnc')?->id() ?? Id::create();
    }

    public function description(): string
    {
        return 'Local non chauffé non décrit';
    }

    public function type_lnc(): TypeLnc
    {
        return TypeLnc::from_type_adjacence_id($this->enum_type_adjacence_id());
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

    public function surface_aue(): float
    {
        return $this->xml()->findOne('.//surface_aue')?->floatval() ?? 0;
    }

    public function surface_aiu(): float
    {
        return $this->xml()->findOneOfOrError([
            './/surface_aiu',
            './/surface_paroi_opaque',
            './/surface_totale_baie',
            './/surface_porte',
        ])->floatval();
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

            foreach ($this->xml()->read_enveloppe()->read_baies() as $item) {
                if ($item->xml()->findOne('.//reference_paroi')?->strval() === $reference)
                    $value += $item->xml()->findOneOrError('.//surface_totale_baie')->floatval();
            }
            foreach ($this->xml()->read_enveloppe()->read_portes() as $item) {
                if ($item->xml()->findOne('.//reference_paroi')?->strval() === $reference)
                    $value += $item->xml()->findOneOrError('.//surface_porte')->floatval();
            }
            return $value;
        }
        return $this->xml()->findOneOfOrError([
            './/surface_paroi_opaque',
            './/surface_totale_baie',
            './/surface_porte',
        ])->floatval();
    }
}
