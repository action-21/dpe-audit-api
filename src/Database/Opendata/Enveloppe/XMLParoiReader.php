<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Mitoyennete;

abstract class XMLParoiReader extends XMLReader
{
    abstract public function surface(): float;

    public function surface_aiu(): float
    {
        return $this->findOne('.//surface_aui')?->floatval() ?? 0;
    }

    public function surface_aue(): float
    {
        return $this->findOne('.//surface_aue')?->floatval() ?? 0;
    }

    /**
     * Liste des identifiants de la paroi
     * 
     * @return string[]
     */
    public function identifiants(): array
    {
        $identifiants = [$this->reference()];

        if ($this->findOne('.//description')) {
            $identifiants[] = $this->findOne('.//description')->reference();
        }
        return $identifiants;
    }

    public function match(array $identifiants): bool
    {
        return count(array_intersect($identifiants, $this->identifiants())) > 0;
    }

    public function id(): Id
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function local_non_chauffe_id(): ?Id
    {
        if ($this->reference_lnc()) {
            return Id::from($this->reference_lnc());
        }
        return $this->mitoyennete() === Mitoyennete::LOCAL_NON_CHAUFFE ? $this->id() : null;
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->reference();
    }

    public function reference_lnc(): ?string
    {
        return $this->findOne('.//reference_lnc')?->reference();
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
