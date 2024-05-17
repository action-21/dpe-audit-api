<?php

namespace App\Domain\Chauffage;

use App\Domain\Chauffage\Entity\{Generateur, GenerateurCollection};
use App\Domain\Chauffage\Enum\{ConfigurationInstallation, TypeInstallation, TypeInstallationSolaire};
use App\Domain\Chauffage\ValueObject\{Fch, NiveauxDesservis, Surface};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Logement\Logement;

/**
 * @property $comptage_individuel absent du modèle de données DPEv2.2
 */
final class InstallationChauffage
{
    public function __construct(
        private readonly \Stringable $id,
        private readonly Logement $logement,
        private string $description,
        private TypeInstallation $type_installation,
        private Surface $surface,
        private NiveauxDesservis $niveaux_desservis,
        private GenerateurCollection $generateur_collection,
        private ?bool $comptage_individuel,
        private ?TypeInstallationSolaire $type_installation_solaire = null,
        private ?Fch $fch,
    ) {
    }

    public static function create(
        Logement $logement,
        string $description,
        TypeInstallation $type_installation,
        NiveauxDesservis $niveaux_desservis,
        Surface $surface,
        ?TypeInstallationSolaire $type_installation_solaire = null,
        ?Fch $fch = null,
        ?bool $comptage_individuel = null,
    ): self {
        return new self(
            id: Id::create(),
            logement: $logement,
            description: $description,
            type_installation: $type_installation,
            niveaux_desservis: $niveaux_desservis,
            surface: $surface,
            fch: $fch,
            comptage_individuel: $type_installation->installation_individuelle() ? null : $comptage_individuel,
            generateur_collection: new GenerateurCollection(),
        );
    }

    public function update(
        string $description,
        ConfigurationInstallation $configuration,
        TypeInstallation $type_installation,
        NiveauxDesservis $niveaux_desservis,
        Surface $surface,
        ?Fch $fch,
        ?bool $comptage_individuel,
    ): self {
        $this->description = $description;
        $this->type_installation = $type_installation;
        $this->niveaux_desservis = $niveaux_desservis;
        $this->surface = $surface;
        $this->fch = $fch;

        if ($type_installation->installation_individuelle()) {
            return $this;
        }
        $this->comptage_individuel = null !== $comptage_individuel ? $comptage_individuel : $this->comptage_individuel;
        return $this;
    }

    public function controle_coherence(): void
    {
        if (null === $this->type_installation_solaire) {
            $this->fch = null;
        }
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function logement(): Logement
    {
        return $this->logement;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function effet_joule(): bool
    {
        return $this->generateur_collection->effet_joule();
    }

    public function type_installation(): TypeInstallation
    {
        return $this->type_installation;
    }

    public function niveaux_desservis(): NiveauxDesservis
    {
        return $this->niveaux_desservis;
    }

    public function surface(): Surface
    {
        return $this->surface;
    }

    public function fch(): ?Fch
    {
        return $this->fch;
    }

    public function comptage_individuel(): ?bool
    {
        return $this->comptage_individuel;
    }

    public function generateur_collection(): GenerateurCollection
    {
        return $this->generateur_collection;
    }

    public function get_generateur(Id $id): ?Generateur
    {
        return $this->generateur_collection->find($id);
    }

    public function add_generateur(Generateur $entity): self
    {
        $this->generateur_collection->add($entity);
        return $this;
    }

    public function remove_generateur(Generateur $entity): self
    {
        $this->generateur_collection->removeElement($entity);
        return $this;
    }
}
