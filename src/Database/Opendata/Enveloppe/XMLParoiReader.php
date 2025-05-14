<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\Lnc\XMLEtsReader;
use App\Database\Opendata\{XMLElement, XMLReader};
use App\Database\Opendata\Enveloppe\Baie\XMLBaieReader;
use App\Database\Opendata\Enveloppe\Porte\XMLPorteReader;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Mitoyennete;

abstract class XMLParoiReader extends XMLReader
{
    abstract public function surface(): float;

    /**
     * @return XMLEtsReader[]
     */
    public function locaux_non_chauffes(): array
    {
        return array_map(
            fn(XMLElement $xml): XMLEtsReader => XMLEtsReader::from($xml),
            $this->findMany('//ets_collection//ets')
        );
    }

    /**
     * @return XMLBaieReader[]
     */
    public function baies(): array
    {
        return array_filter(
            $this->enveloppe()->baies(),
            fn(XMLBaieReader $reader) => $reader->reference_paroi() === $this->reference()
        );
    }

    /**
     * @return XMLPorteReader[]
     */
    public function portes(): array
    {
        return array_filter(
            $this->enveloppe()->portes(),
            fn(XMLPorteReader $reader) => $reader->reference_paroi() === $this->reference()
        );
    }

    public function surface_aiu(): float
    {
        return $this->findOne('.//surface_aui')?->floatval() ?? 0;
    }

    public function surface_aue(): float
    {
        return $this->findOne('.//surface_aue')?->floatval() ?? 0;
    }

    public function id(): Id
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function paroi_id(): ?Id
    {
        return null;
    }

    public function local_non_chauffe_accessible(): bool
    {
        if ($this->mitoyennete() !== Mitoyennete::LOCAL_NON_CHAUFFE) {
            return false;
        }
        foreach ($this->locaux_non_chauffes() as $reader) {
            if ($reader->reference() === $this->reference_lnc()) {
                return true;
            }
        }
        return false;
    }

    public function local_non_chauffe_virtuel(): bool
    {
        if ($this->mitoyennete() !== Mitoyennete::LOCAL_NON_CHAUFFE) {
            return false;
        }
        if ($this->local_non_chauffe_accessible()) {
            return false;
        }
        if ($this->enum_type_adjacence_id() === 10) {
            return false;
        }
        if ($this->surface_aue() === 0) {
            return false;
        }
        if ($this->paroi_id()) {
            return false;
        }
        return true;
    }

    public function local_non_chauffe_id(): ?Id
    {
        if ($this->mitoyennete() !== Mitoyennete::LOCAL_NON_CHAUFFE) {
            return null;
        }
        if ($this->local_non_chauffe_accessible()) {
            return Id::from($this->reference_lnc());
        }
        if ($this->local_non_chauffe_virtuel()) {
            return $this->paroi_id() ?? $this->id();
        }
        return null;
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->reference();
    }

    public function reference_lnc(): ?string
    {
        return $this->findOne('.//reference_lnc')?->reference();
    }

    public function reference_paroi(): ?string
    {
        return $this->findOne('.//reference_paroi')?->reference();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Mur non décrit';
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->enum_cfg_isolation_lnc_id() === 1
            ? Mitoyennete::LOCAL_NON_ACCESSIBLE
            : Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function enum_type_adjacence_id(): int
    {
        return $this->findOneOrError('.//enum_type_adjacence_id')->intval();
    }

    public function enum_cfg_isolation_lnc_id(): ?int
    {
        return $this->findOne('.//enum_cfg_isolation_lnc_id')?->intval();
    }
}
