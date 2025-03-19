<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\{XMLElement, XMLReader};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Enum\{BouclageReseau, IsolationReseau, UsageEcs};
use App\Domain\Ecs\ValueObject\{Reseau, Solaire};

final class XMLInstallationReader extends XMLReader
{
    /** @return XMLGenerateurReader[] */
    public function read_generateurs(): array
    {
        return \array_map(
            fn(XMLElement $xml): XMLGenerateurReader => XMLGenerateurReader::from($xml),
            $this->xml()->findMany('.//generateur_ecs_collection//generateur_ecs')
        );
    }

    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->strval();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Installation non décrite';
    }

    public function reseau(): Reseau
    {
        return new Reseau(
            alimentation_contigues: $this->alimentation_contigues(),
            niveaux_desservis: $this->niveaux_desservis(),
            isolation_reseau: $this->isolation_reseau() ?? false,
            type_bouclage: $this->type_bouclage(),
        );
    }

    public function solaire(): ?Solaire
    {
        return $this->usage_solaire() ? new Solaire(
            usage: $this->usage_solaire(),
            annee_installation: $this->annee_installation_solaire(),
            fecs: $this->fecs_saisi(),
        ) : null;
    }

    public function usage_solaire(): ?UsageEcs
    {
        return ($id = $this->enum_type_installation_solaire_id()) ? UsageEcs::from_enum_type_installation_solaire_id($id) : null;
    }

    public function annee_installation_solaire(): ?int
    {
        return match ($this->enum_type_installation_solaire_id()) {
            3 => $this->xml()->annee_etablissement(),
            default => null,
        };
    }

    public function alimentation_contigues(): bool
    {
        return match ($this->tv_rendement_distribution_ecs_id()) {
            1, 4, 6 => true,
            default => false,
        };
    }

    public function isolation_reseau(): IsolationReseau
    {
        return match ($this->reseau_distribution_isole()) {
            true => IsolationReseau::ISOLE,
            false => IsolationReseau::NON_ISOLE,
            null => IsolationReseau::INCONNU,
        };
    }

    public function installation_collective(): bool
    {
        return \in_array($this->enum_type_installation_id(), [2, 3, 4]);
    }

    public function niveaux_desservis(): int
    {
        return $this->xml()->findOneOrError('.//nombre_niveau_installation_ecs')->intval();
    }

    public function type_bouclage(): ?BouclageReseau
    {
        return ($id = $this->enum_bouclage_reseau_ecs_id())
            ? BouclageReseau::from_enum_bouclage_reseau_ecs_id($id)
            : null;
    }

    public function fecs_saisi(): ?float
    {
        return $this->xml()->findOne('.//fecs_saisi')?->floatval();
    }

    public function enum_cfg_installation_ecs_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_cfg_installation_ecs_id')->intval();
    }

    public function enum_type_installation_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_installation_id')->intval();
    }

    public function enum_bouclage_reseau_ecs_id(): ?int
    {
        return $this->xml()->findOne('.//enum_bouclage_reseau_ecs_id')?->intval();
    }

    public function enum_type_installation_solaire_id(): ?int
    {
        return $this->xml()->findOne('.//enum_type_installation_solaire_id')?->intval();
    }

    public function tv_rendement_distribution_ecs_id(): int
    {
        return $this->xml()->findOneOrError('.//tv_rendement_distribution_ecs_id')->intval();
    }

    public function reseau_distribution_isole(): ?bool
    {
        return $this->xml()->findOne('.//reseau_distribution_isole')?->boolval();
    }

    public function surface_habitable(): float
    {
        return $this->xml()->findOneOrError('.//surface_habitable')->floatval();
    }

    public function nombre_logement(): int
    {
        return $this->xml()->findOneOrError('.//nombre_logement')->intval();
    }

    // Données intermédiaires

    public function rendement_distribution(): float
    {
        return $this->xml()->findOneOrError('.//rendement_distribution')->floatval();
    }

    public function fecs(): float
    {
        return $this->xml()->findOne('.//fecs')->floatval();
    }
}
