<?php

namespace App\Domain\Ecs;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Entity\{Generateur, GenerateurCollection};
use App\Domain\Ecs\Enum\{BouclageReseau, TypeInstallation, TypeInstallationSolaire};
use App\Domain\Ecs\ValueObject\{Fecs, NiveauxDesservis};
use App\Domain\Logement\Logement;

/**
 * @see https://github.com/renolab/audit/discussions/20
 */
class InstallationEcs
{
    public function __construct(
        private readonly Id $id,
        private readonly Logement $logement,
        private string $description,
        private bool $pieces_contigues,
        private ?bool $reseau_distribution_isole,
        private NiveauxDesservis $niveaux_desservis,
        private TypeInstallation $type_installation,
        private BouclageReseau $bouclage_reseau,
        private GenerateurCollection $generateur_collection,
        private ?TypeInstallationSolaire $type_installation_solaire,
        private ?Fecs $fecs,
    ) {
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

    public function reseau_distribution_isole(): ?bool
    {
        return $this->reseau_distribution_isole;
    }

    public function pieces_contigues(): bool
    {
        return $this->pieces_contigues;
    }

    public function niveaux_desservis(): NiveauxDesservis
    {
        return $this->niveaux_desservis;
    }

    public function type_installation(): TypeInstallation
    {
        return $this->type_installation;
    }

    public function bouclage_reseau(): BouclageReseau
    {
        return $this->bouclage_reseau;
    }

    public function type_installation_solaire(): ?TypeInstallationSolaire
    {
        return $this->type_installation_solaire;
    }

    public function fecs(): ?Fecs
    {
        return $this->fecs;
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

    public function remove_ventilation(Generateur $entity): self
    {
        $this->generateur_collection->removeElement($entity);
        return $this;
    }
}
