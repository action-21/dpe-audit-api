<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\XMLElement;
use App\Domain\Common\Identifier\Reference;
use App\Domain\Ecs\Enum\{BouclageReseau, ConfigurationInstallation, TypeInstallation, TypeInstallationSolaire};
use App\Domain\Ecs\ValueObject\{Fecs, NiveauxDesservis};

final class XMLInstallationEcsReader
{
    private XMLElement $xml;

    public function __construct(private XMLGenerateurReader $generateur_reader)
    {
    }

    // * Données d'entrée

    public function id(): \Stringable
    {
        return Reference::create($this->reference());
    }

    public function reference(): string
    {
        return $this->xml->findOneOrError('.//reference');
    }

    public function description(): string
    {
        return $this->xml->findOne('.//description')?->getValue() ?? 'Installation non décrite';
    }

    public function enum_cfg_installation_ecs_id(): int
    {
        return (int) $this->xml->findOneOrError('.//enum_cfg_installation_ecs_id');
    }

    public function configuration(): ConfigurationInstallation
    {
        return ConfigurationInstallation::from_enum_cfg_installation_ecs_id($this->enum_cfg_installation_ecs_id());
    }

    public function enum_bouclage_reseau_ecs_id(): int
    {
        return (int) $this->xml->findOneOrError('.//enum_bouclage_reseau_ecs_id');
    }

    public function bouclage_reseau(): BouclageReseau
    {
        return BouclageReseau::from_enum_bouclage_reseau_ecs_id($this->enum_bouclage_reseau_ecs_id());
    }

    public function enum_type_installation_id(): int
    {
        return (int) $this->xml->findOneOrError('.//enum_type_installation_id');
    }

    public function type_installation(): TypeInstallation
    {
        return TypeInstallation::from_enum_type_installation_id($this->enum_type_installation_id());
    }

    public function enum_type_installation_solaire_id(): ?int
    {
        return ($value = $this->xml->findOne('.//enum_type_installation_solaire_id')) ? (int) $value : null;
    }

    public function type_installation_solaire(): ?TypeInstallationSolaire
    {
        return ($id = $this->enum_type_installation_solaire_id())
            ? TypeInstallationSolaire::from_enum_type_installation_solaire_id($id)
            : null;
    }

    public function reseau_distribution_isole(): ?bool
    {
        return ($value = $this->xml->findOne('.//reseau_distribution_isole')) ? (bool)(int) $value : null;
    }

    public function ratio_virtualisation(): ?float
    {
        return ($value = $this->xml->findOne('.//ratio_virtualisation')) ? (float) $value : null;
    }

    public function cle_repartition_ecs(): ?float
    {
        return ($value = $this->xml->findOne('.//cle_repartition_ecs')) ? (float) $value : null;
    }

    public function surface_habitable(): float
    {
        return (float) $this->xml->findOneOrError('.//surface_habitable');
    }

    public function nombre_logement(): int
    {
        return (int) $this->xml->findOneOrError('.//nombre_logement');
    }

    public function rdim(): float
    {
        return (float) $this->xml->findOneOrError('.//rdim');
    }

    public function nombre_niveau_installation_ecs(): int
    {
        return (int) $this->xml->findOneOrError('.//nombre_niveau_installation_ecs');
    }

    public function niveaux_desservis(): NiveauxDesservis
    {
        return NiveauxDesservis::from($this->nombre_niveau_installation_ecs());
    }

    public function fecs_saisi(): ?Fecs
    {
        return ($value = $this->xml->findOne('.//fecs_saisi')) ? Fecs::from((float) $value) : null;
    }

    // * Données d'entrée déduites

    /**
     * @see https://github.com/renolab/audit/discussions/20
     */
    public function pieces_contigues(): bool
    {
        return false;
    }

    // * Données intermédiaires

    public function besoin_ecs(): float
    {
        return (float) $this->xml->findOneOrError('.//besoin_ecs');
    }

    public function besoin_ecs_depensier(): float
    {
        return (float) $this->xml->findOneOrError('.//besoin_ecs_depensier');
    }

    public function fecs(): ?float
    {
        return ($value = $this->xml->findOne('.//fecs')) ? (float) $value : null;
    }

    public function production_ecs_solaire(): ?float
    {
        return ($value = $this->xml->findOne('.//production_ecs_solaire')) ? (float) $value : null;
    }

    public function conso_ecs(): float
    {
        return (float) $this->xml->findOneOrError('.//conso_ecs');
    }

    public function conso_ecs_depensier(): float
    {
        return (float) $this->xml->findOneOrError('.//conso_ecs_depensier');
    }

    public function generateur_reader(): XMLGenerateurReader
    {
        return $this->generateur_reader->read($this->xml, $this);
    }

    public static function apply(XMLElement $xml): bool
    {
        return true;
    }

    /**
     * TODO: identifier les installations par appartement dans le cas d'un Audit-DPE immeuble
     */
    public function read(XMLElement $xml): self
    {
        $this->xml = $xml->findOneOrError('.//installation_ecs_collection/installation_ecs');
        return $this;
    }
}
