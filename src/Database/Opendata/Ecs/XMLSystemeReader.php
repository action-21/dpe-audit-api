<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Enum\{BouclageReseau, IsolationReseau};

final class XMLSystemeReader extends XMLReader
{
    public function installation(): XMLInstallationReader
    {
        return XMLInstallationReader::from($this->findOneOrError('./ancestor::installation_ecs'));
    }

    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->reference();
    }

    public function generateur_id(): Id
    {
        return $this->id();
    }

    public function installation_id(): Id
    {
        return $this->installation()->id();
    }

    public function stockage(): bool
    {
        return $this->enum_type_stockage_ecs_id() === 2;
    }

    public function volume_stockage(): ?float
    {
        return $this->stockage()
            ? $this->findOneOrError('.//volume_stockage')->floatval()
            : null;
    }

    public function position_volume_chauffe_stockage(): bool
    {
        return $this->stockage()
            ? $this->findOne('.//position_volume_chauffe_stockage')?->boolval() ?? false
            : null;
    }

    public function alimentation_contigues(): bool
    {
        return match ($this->installation()->tv_rendement_distribution_ecs_id()) {
            1, 4, 6 => true,
            default => false,
        };
    }

    public function niveaux_desservis(): int
    {
        return $this->installation()->findOneOrError('.//nombre_niveau_installation_ecs')->intval();
    }

    public function isolation_reseau(): ?IsolationReseau
    {
        return match ($this->installation()->reseau_distribution_isole()) {
            true => IsolationReseau::ISOLE,
            false => IsolationReseau::NON_ISOLE,
            default => null,
        };
    }

    public function bouclage_reseau(): ?BouclageReseau
    {
        return ($id = $this->installation()->enum_bouclage_reseau_ecs_id())
            ? BouclageReseau::from_enum_bouclage_reseau_ecs_id($id)
            : null;
    }

    public function enum_type_stockage_ecs_id(): int
    {
        return $this->findOneOrError('.//enum_type_stockage_ecs_id')->intval();
    }
}
