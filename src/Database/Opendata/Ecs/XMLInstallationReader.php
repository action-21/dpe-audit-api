<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\{XMLElement, XMLReader};
use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Ecs\Enum\UsageEcs;

final class XMLInstallationReader extends XMLReader
{
    /** @return XMLGenerateurReader[] */
    public function generateurs(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLGenerateurReader => XMLGenerateurReader::from($xml),
            $this->findMany('.//generateur_ecs_collection//generateur_ecs')
        );
    }

    public function id(): Id
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->strval();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Installation non décrite';
    }

    public function usage_solaire(): ?UsageEcs
    {
        return ($id = $this->enum_type_installation_solaire_id())
            ? UsageEcs::from_enum_type_installation_solaire_id($id)
            : null;
    }

    public function annee_installation_solaire(): ?Annee
    {
        return match ($this->enum_type_installation_solaire_id()) {
            3 => $this->audit()->annee_etablissement(),
            default => null,
        };
    }

    public function installation_collective(): bool
    {
        return \in_array($this->enum_type_installation_id(), [2, 3, 4]);
    }

    public function fecs_saisi(): ?float
    {
        return $this->findOne('.//fecs_saisi')?->floatval();
    }

    public function enum_cfg_installation_ecs_id(): int
    {
        return $this->findOneOrError('.//enum_cfg_installation_ecs_id')->intval();
    }

    public function enum_type_installation_id(): int
    {
        return $this->findOneOrError('.//enum_type_installation_id')->intval();
    }

    public function enum_bouclage_reseau_ecs_id(): ?int
    {
        return $this->findOne('.//enum_bouclage_reseau_ecs_id')?->intval();
    }

    public function enum_type_installation_solaire_id(): ?int
    {
        return $this->findOne('.//enum_type_installation_solaire_id')?->intval();
    }

    public function tv_rendement_distribution_ecs_id(): int
    {
        return $this->findOneOrError('.//tv_rendement_distribution_ecs_id')->intval();
    }

    public function reseau_distribution_isole(): ?bool
    {
        return $this->findOne('.//reseau_distribution_isole')?->boolval();
    }

    public function surface(): float
    {
        return $this->findOneOrError('.//surface_habitable')->floatval();
    }

    public function nombre_logement(): int
    {
        return $this->findOneOrError('.//nombre_logement')->intval();
    }

    // Données intermédiaires

    public function rendement_distribution(): float
    {
        return $this->findOneOrError('.//rendement_distribution')->floatval();
    }

    public function fecs(): float
    {
        return $this->findOne('.//fecs')->floatval();
    }
}
